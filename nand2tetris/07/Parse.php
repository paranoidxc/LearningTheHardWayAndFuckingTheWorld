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

        $this->arg1 = '';
        $this->arg2 = '';

        $line = $this->line;
        $tokens = explode(' ', $line);

        foreach ($tokens as $str) {
            if (in_array($str, $arithmetic)) {
                $this->arg1 = $str;
                $r = self::$C_ARITHMETIC;
                break;
            }

            if (FALSE !== strpos($line, 'push')) {
                $r = self::$C_PUSH;
                $this->arg1 = $tokens[1];
                $this->arg2 = $tokens[2];
                break;
            }

            if (FALSE !== strpos($line, 'pop')) {
                $r = self::$C_POP;
                $this->arg1 = $tokens[1];
                $this->arg2 = $tokens[2];
                break;
            }
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
}
