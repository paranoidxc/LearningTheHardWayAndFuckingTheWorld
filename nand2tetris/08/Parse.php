<?php
class Parse
{
    private $cur_line = 0;
    private $total_line = 0;
    private $lines = [];

    public static $C_ARITHMETIC = 1;
    public static $C_PUSH = 2;
    public static $C_POP = 3;
    public static $C_LABEL = 4;
    public static $C_GOTO = 5;
    public static $C_IF = 6;
    public static $C_FUNCTION = 7;
    public static $C_RETURN = 8;
    public static $C_CALL = 9;

    private $type;
    private $line;
    private $arg1;
    private $arg2;

    function __construct($lines = [])
    {
        $this->lines = $lines;
        $this->total_line = count($this->lines);
    }

    function hasMoreCommands()
    {
        return $this->cur_line < $this->total_line;
    }

    function advance()
    {
        $this->line = $this->lines[$this->cur_line++];
    }

    function commandType()
    {
        $r = '';

        $arithmetic = CodeWrite::getOpList();

        $tokens = explode(' ', $this->line);
        $this->arg1 = $tokens[1];
        $this->arg2 = $tokens[2];

        if (in_array($tokens[0], $arithmetic)) {
            $this->arg1 = $tokens[0];
            $r = self::$C_ARITHMETIC;
        } else if ('push' == $tokens[0]) {
            $r = self::$C_PUSH;
        } else if ('pop' == $tokens[0]) {
            $r = self::$C_POP;
        } else if ('label' == $tokens[0]) {
            $r = self::$C_LABEL;
        } else if ('goto' == $tokens[0]) {
            $r = self::$C_GOTO;
        } else if ('if-goto' == $tokens[0]) {
            $r = self::$C_IF;
        } else if ('return' == $tokens[0]) {
            $r = self::$C_RETURN;
        } else if ('call' == $tokens[0]) {
            $r = self::$C_CALL;
        } else if ('function' == $tokens[0]) {
            $r = self::$C_FUNCTION;
        }

        return $r;
    }

    function arg1()
    {
        return $this->arg1;
    }

    function arg2()
    {
        return $this->arg2;
    }

    function getLineContent()
    {
        return $this->line;
    }
}
