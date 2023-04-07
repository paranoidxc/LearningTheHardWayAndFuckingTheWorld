<?php
class VMWriter
{
    public $lines = [];
    public $f;

    function constructor()
    {
    }

    function writePush($segment, $index)
    {
    }

    function writePop($segment, $index)
    {
    }

    function writeArithmetic($command)
    {
    }

    function writeLabel($label)
    {
        $this->lines[] = "push {$label}";
    }

    function writeGoto($label)
    {
    }

    function writeIf($label)
    {
    }

    function writeCall($name, $nArgs, $is_para=0)
    {
        if ($nArgs || $is_para) {
            $this->lines[] = "call {$name} {$nArgs}";
        } else {
            $this->lines[] = "{$name}";
        }
    }

    function writeFunction($name, $nArgs)
    {
    }

    function writeReturn()
    {
    }

    function close($f)
    {
        $this->lines[] = "";
        file_put_contents($f, join(PHP_EOL, $this->lines));
    }

    function write($lines)
    {
        $r = is_array($lines) ? $lines : [$lines];

        foreach ($r as $l) {
            $this->lines[] = $l;
        }
    }
}
