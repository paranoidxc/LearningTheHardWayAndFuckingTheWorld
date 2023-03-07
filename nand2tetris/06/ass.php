<?php
class Parser
{
    private $cur_line = 0;
    private $total_lines = 0;
    private $file_contents = [];

    public static $A_ = 0;
    public static $C_ = 1;
    public static $L_ = 2;

    public static $MAP_COMP = [
        '0'  => '0101010',
        '1'  => '0111111',
        '-1' => '0111010',
        'D'  => '0001100',
        'A'  => '0110000',
        '!D' => '0001101',
        '!A' => '0110001',
        '-D' => '0001111',
        '-A' => '0110011',
        'D+1' => '0011111',
        'A+1' => '0110111',
        'D-1' => '0001110',
        'A-1' => '0110010',
        'D+A' => '0000010',
        'D-A' => '0010011',
        'A-D' => '0000111',
        'D&A' => '0000000',
        'D|A' => '0010101',
        'M'     => '1110000',
        '!M'    => '1110001',
        '-M'    => '1110011',
        'M+1'   => '1110111',
        'M-1'   => '1110010',
        'D+M'   => '1000010',
        'D-M'   => '1010011',
        'M-D'   => '1000111',
        'D&M'   => '1000000',
        'D|M'   => '1010101',
    ];

    public static $MAP_JUMP = [
        'JGT' => '001',
        'JEQ' => '010',
        'JGE' => '011',
        'JLT' => '100',
        'JNE' => '101',
        'JLE' => '110',
        'JMP' => '111',
    ];

    function __construct($f)
    {
        $c = file_get_contents($f);
        $this->file_contents = explode("\r\n", $c);
        $this->total_lines = count($this->file_contents);
    }

    function reset()
    {
        $this->cur_line = 0;
    }

    function hasMoreCommands()
    {
        return $this->cur_line < $this->total_lines;
    }

    function advance()
    {
        return $this->file_contents[$this->cur_line++];
    }

    function commandType($str)
    {
        $str = trim($str);
        if (strlen($str)) {
            if (!($str[0] == '/' and $str[1] == '/')) {
                if ('@' == $str[0]) {
                    return self::$A_;
                } elseif ('(' == $str[0]) {
                    return self::$L_;
                } else {
                    return self::$C_;
                }
            }
        }
    }

    function symbol($str)
    {
        return str_replace(['@', '(', ')'], '', $str);
    }

    function dest($str)
    {
        $r = ['A' => 0, 'D' => 0, 'M' => 0];
        for ($i = 0; $i < strlen($str); $i++) {
            $r[$str[$i]] = 1;
        }

        return join("", $r);
    }

    function comp($str)
    {
        return self::$MAP_COMP[$str];
    }

    function jump($str)
    {
        $str = strtoupper($str);
        $v = '000';
        if (strlen($str)) {
            $v = self::$MAP_JUMP[$str];
        }
        return $v;
    }
}

class SymbolTable
{
    private $list = [
        'SP' => 0,
        'LCL' => 1,
        'ARG' => 2,
        'THIS' => 3,
        'THAT' => 4,
        'R0' => 0,
        'R1' => 1,
        'R2' => 2,
        'R3' => 3,
        'R4' => 4,
        'R5' => 5,
        'R6' => 6,
        'R7' => 7,
        'R8' => 8,
        'R9' => 9,
        'R10' => 10,
        'R11' => 11,
        'R12' => 12,
        'R13' => 13,
        'R14' => 14,
        'R15' => 15,
        'SCREEN' => 16384,
        'KEY' => 24576,
    ];

    private $ram_next = 16;

    function addEntry($symbol, $address)
    {
        $this->list[$symbol] = $address;
    }

    function contains($symbol)
    {
        return isset($this->list[$symbol]);
    }

    function GetAddress($symbol)
    {
        return $this->list[$symbol];
    }

    function getNextRam()
    {
        return $this->ram_next++;
    }
}

function decbin16($dec)
{
    return sprintf("%016d", decbin($dec));
}

function outLog($str)
{
    print_r($str);
    print_r("<BR>");
}

outLog("BEGIN");

$files[] = '../06/add/Add.asm';
$files[] = '../06/max/max.asm';
$files[] = '../06/max/maxL.asm';
$files[] = '../06/pong/pong.asm';
$files[] = '../06/pong/pongL.asm';
$files[] = '../06/rect/Rect.asm';
$files[] = '../06/rect/RectL.asm';
foreach ($files as $f) {
    $pathinfo = pathinfo($f);
    $hack_file = $pathinfo['dirname'] . '/' . $pathinfo['filename'] . '_hk' . '.hack';

    $parse = new Parser($f);
    $symbol_tb = new SymbolTable();

    $pos = 0;
    while ($parse->hasMoreCommands()) {
        $line = $parse->advance();
        $type = $parse->commandType(trim($line));
        if (0 === $type or 1 === $type) {
            $pos += 1;
        } else if (2 === $type) {
            $symbol = $parse->symbol($line);
            $symbol_tb->addEntry($symbol, $pos);
        }
    }
    $parse->reset();
    $hack = [];
    while ($parse->hasMoreCommands()) {
        $line = $parse->advance();
        list($line, $nul) = explode("//", $line);
        $line = trim($line);
        $line = str_replace(' ', '', $line);
        $type = $parse->commandType($line);
        if (Parser::$A_ === $type) {
            $symbol = $parse->symbol($line);
            $val = '';
            if (is_numeric($symbol)) {
                $val = $symbol;
            } else {
                if ($symbol_tb->contains($symbol)) {
                    $val = $symbol_tb->GetAddress($symbol);
                } else {
                    //outLog("Var {$symbol}");
                    $val = $symbol_tb->getNextRam();
                    //outLog($add);
                    $symbol_tb->addEntry($symbol, $val);
                }
            }
            if (strlen($val)) {
                $hack[] = decbin16($val);
            }
        } else if (Parser::$C_ === $type) {
            if (strpos($line, "=") === false) {
                $dest = '';
                list($comp, $jump) = explode(";", $line);
            } else {
                list($dest, $s2) = explode("=", $line);
                list($comp, $jump) = explode(";", $s2);
            }
            $t = [
                "111",
                $parse->comp($comp),
                $parse->dest($dest),
                $parse->jump($jump)
            ];
            $hack[] = join("", $t);
        } else if (Parser::$L_ === $type) {
            //do nothing
        }
    }
    file_put_contents($hack_file, join("\r\n", $hack));
    outLog("Gen File " . $hack_file . " Okey");
}
