<?php
class CodeWrite
{
    private $asms = [];
    private $file;
    private $vm_filename = '';
    private $jump_address = 0;

    private static $MAP_SEGMENT = [
        'local'     => 'LCL',
        'argument'  => 'ARG',
        'this'      => 'THIS',
        'that'      => 'THAT',
        'temp'      => 5
    ];

    private static $MAP_COMPARE_JUMP = [
        'eq'  => 'D;JNE',
        'lt'  => 'D;JGE',
        'gt'  => 'D;JLE',
        'add' => 'M=M+D',
        'sub' => 'M=M-D',
        'and' => 'M=M&D',
        'or'  => 'M=M|D',
        'not' => 'M=!D',
        'neg' => 'M=-D',
    ];

    private function getJumpAddress($op)
    {
        $this->jump_address += 1;
        return "END_{$op}.{$this->jump_address}";
    }

    private function pushAsmCode($l = [])
    {
        if (is_string($l)) {
            $this->asms[] = $l;
        } else {
            foreach ($l as $asm) {
                $this->asms[] = $asm;
            }
        }
    }

    static function getOpList()
    {
        return array_keys(self::$MAP_COMPARE_JUMP);
    }

    function setVmFileName($vm)
    {
        $this->vm_filename = $vm;
    }

    function setFileName($filename)
    {
        $this->file = $filename;
        $this->pushAsmCode(
            [
                "@256",
                "D=A",
                "@SP",
                "M=D",
            ]
        );

        $this->writeCall("Sys.init", 0);
    }

    function writeArithmetic($op)
    {
        if (in_array($op, ["not", "neg"])) {
            $code = self::$MAP_COMPARE_JUMP[$op];
            $this->pushAsmCode([
                '@SP',
                'A=M-1',
                'D=M',
                "{$code}",
            ]);
        } else if (in_array($op, ["add", "sub", "and", "or"])) {
            $code = self::$MAP_COMPARE_JUMP[$op];
            $this->pushAsmCode([
                '@SP',
                'AM=M-1',   //SP--
                'D=M',      //D=R[SP]  y=d
                'A=A-1',    //direct use stack
                "{$code}"
            ]);
        } else if (in_array($op, ["eq", "lt", "gt"])) {
            $jmpaddr = $this->getJumpAddress($op);
            $code = self::$MAP_COMPARE_JUMP[$op];
            $this->pushAsmCode([
                '@SP',
                'AM=M-1',   //SP--
                'D=M',      //D=R[SP] D=y
                'A=A-1',    //SP--
            ]);
            $this->pushAsmCode('D=M-D');
            $this->pushAsmCode([
                "M=0",      // 预设false
                "@{$jmpaddr}", //跳转
                "{$code}",
                '@SP',
                'A=M-1',
                "M=-1",
                "({$jmpaddr})",
            ]);
        }
    }

    function writePushPop($type, $segment, $index)
    {
        $r = [];
        if (Parse::$C_PUSH == $type) {
            if (in_array($segment, ["constant", "temp", "static", "pointer"])) {
                if ("constant" == $segment) {
                    // d = i
                    $r[] = "@{$index}";
                    $r[] = "D=A";
                } else if ("static" == $segment) {
                    $index = "{$this->vm_filename}.{$index}";
                    $r[] = "@{$index}";
                    $r[] = "D=M";
                } else {
                    if ("temp" == $segment) {
                        $index = 5 + $index;
                    } else if ("static" == $segment) {
                    } else if ("pointer" == $segment) {
                        if (0 == $index) {
                            $index = self::$MAP_SEGMENT["this"];
                        } else {
                            $index = self::$MAP_SEGMENT["that"];
                        }
                    }
                    $r[] = "@{$index}";
                    $r[] = "D=M";
                }
                // ram[sp] = d
                $r[] = "@SP";
                $r[] = "A=M";
                $r[] = "M=D";
                // sp ++
                $r[] = "@SP";
                $r[] = "M=M+1";
            } else {
                $ram_ident = self::$MAP_SEGMENT[$segment];
                // segment_address + index
                $r[] = "@{$index}";
                $r[] = "D=A";
                $r[] = "@{$ram_ident}";
                $r[] = "A=M";
                $r[] = "A=D+A"; //D = addr+index
                $r[] = "D=M"; //D = addr+index

                // ram[sp] = d
                $r[] = "@SP";
                $r[] = "A=M";
                $r[] = "M=D";

                // sp ++
                $r[] = "@SP";
                $r[] = "M=M+1";
            }
        } else if (Parse::$C_POP == $type) {
            // sp --
            $r[] = "@SP";
            $r[] = "M=M-1";

            if (in_array($segment, ["static", "temp", "pointer"])) {
                if ("temp" == $segment) {
                    $index = 5 + $index;
                } else if ("static" == $segment) {
                    $index = "{$this->vm_filename}.{$index}";
                } else if ("pointer" == $segment) {
                    if (0 == $index) {
                        $index = self::$MAP_SEGMENT["this"];
                    } else {
                        $index = self::$MAP_SEGMENT["that"];
                    }
                }
                $r[] = "@SP";
                $r[] = "A=M";
                $r[] = "D=M";

                $r[] = "@{$index}";
                $r[] = "M=D";
            } else {
                $ram_ident = self::$MAP_SEGMENT[$segment];
                $tmp_ram = "@R13";
                // segment_address + index
                $r[] = "@{$index}";
                $r[] = "D=A";
                $r[] = "@{$ram_ident}";
                $r[] = "A=M";
                $r[] = "D=D+A";
                // address to tmp_ram
                $r[] = $tmp_ram;
                $r[] = "M=D";

                // ram[sp] -> ram[segment];
                $r[] = "@SP";
                $r[] = "A=M";
                $r[] = "D=M";

                $r[] = $tmp_ram;
                $r[] = "A=M";
                $r[] = "M=D";
            }
        }

        //outLog("type = {$type}");
        //outLog("segment = {$segment}");
        //outLog("index = {$index}");
        //outLog($r);
        //$this->asms = array_merge($this->asms, $r);
        $this->pushAsmCode($r);
    }

    function writeLabel($str)
    {
        $t = [
            "($str)"
        ];
        $this->pushAsmCode($t);
    }

    function writeGoto($str, $return = FALSE)
    {
        $t = [
            "@{$str}",
            "0;JMP",
        ];

        if ($return) {
            return $t;
        }
        $this->pushAsmCode($t);
    }

    function writeIf($str)
    {
        $t = [
            '@SP',
            'AM=M-1',
            'D=M',
            "@{$str}",
            'D;JNE',
        ];

        $this->pushAsmCode($t);
    }

    function writeCall($fn, $num)
    {
        $addr = $this->getJumpAddress($fn);
        // push return addr
        $t = [
            "@{$addr}",
            "D=A",

            "@SP",
            "A=M",
            "M=D",

            "@SP",
            "M=M+1",
        ];

        // push segment
        $points = ['LCL', 'ARG', 'THIS', 'THAT'];
        foreach ($points as $name) {
            $t = array_merge($t, [
                "@{$name}",
                "D=M",

                "@SP",
                "A=M",
                "M=D",

                "@SP",
                "M=M+1",
            ]);
        }
        $num = $num + 5;
        $t = array_merge($t, [
            //ARG = SP-n-5
            "@{$num}",
            "D=A",

            "@SP",
            "D=M-D",

            "@ARG",
            "M=D",

            //LCL = SP
            "@SP",
            "D=M",
            "@LCL",
            "M=D",
        ]);

        //goto f
        $t = array_merge($t, $this->writeGoto($fn, TRUE));
        $t[] = "($addr)";
        $this->pushAsmCode($t);
    }

    function writeReturn()
    {
        $t = [
            // R13 = endFrame = LCL
            "@LCL",
            "D=M",
            "@R13",
            "M=D",

            //return addr
            "@5",
            "A=D-A",
            "D=M",
            "@R14",
            "M=D",

            // arg = return value
            // pop to D
            "@SP",
            "A=M-1",
            "D=M",
            //arg = d
            "@ARG",
            "A=M",
            "M=D",

            // SP address = arg+1
            "@ARG",
            "D=M+1",
            "@SP",
            "M=D",
        ];

        $points = ['THAT', 'THIS', 'ARG', 'LCL'];
        foreach ($points as $name) {
            $t = array_merge($t, [
                "@R13",
                "AM=M-1",
                "D=M",
                "@{$name}",
                "M=D"
            ]);
        }

        // go return addr
        $t = array_merge($t, [
            "@R14",
            "A=M",
            "0;JMP",
        ]);

        $this->pushAsmCode($t);
    }

    function writeFunction($fn, $num)
    {
        $t = [
            "({$fn})"
        ];
        for ($i = 0; $i < $num; $i++) {
            $t[] = "@0";
            $t[] = "D=A";

            $t[] = "@SP";
            $t[] = "A=M";
            $t[] = "M=D";

            $t[] = "@SP";
            $t[] = "M=M+1";
        }

        $this->pushAsmCode($t);
    }

    function close()
    {
        if ($this->file) {
            $this->asms[] = "(END)";
            $this->asms[] = "@END";
            $this->asms[] = "0;JMP";
            file_put_contents($this->file, join(PHP_EOL, $this->asms));
        }
        return $this->asms;
    }
}
