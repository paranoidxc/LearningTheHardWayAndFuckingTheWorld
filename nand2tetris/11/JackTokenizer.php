<?php
class JackTokenizer
{
    static $LIST_KEYWORD = [
        'class',
        'constructor',
        'function',
        'method',
        'field',
        'static',
        'var',
        'int',
        'char',
        'boolean',
        'void',
        'true',
        'false',
        'null',
        'this',
        'let',
        'do',
        'if',
        'else',
        'while',
        'return'
    ];

    static $LIST_SYMBOL = [
        '{',
        '}',
        '(',
        ')',
        '[',
        ']',
        '.',
        ',',
        ';',
        '+',
        '-',
        '*',
        '/',
        '&',
        '|',
        '<',
        '>',
        '=',
        '~'
    ];

    static $T_KEYWORD = 1;
    static $T_SYMBOL = 2;
    static $T_IDENTIFIER = 3;
    static $T_INT_CONST = 4;
    static $T_STRING_CONST = 5;

    private $cur_idx = 0;
    private $total_tokens = 0;
    private $tokens = [];

    function __construct($lines)
    {
        //outLog($lines);
        $l = 1;
        foreach ($lines as $line) {
            //outLog($l);
            $this->tokens = array_merge($this->tokens, $this->parseLine($line, $l));
            $l++;
        }
        //outLog($this->tokens);
        $this->total_tokens = count($this->tokens);
    }

    function parseLine($line, $l)
    {
        //outLog($line);
        $r = [];
        $is_string_start = 0;

        $str_s_idx = 0;
        $list_i = 0;
        for ($i = 0; $i < strlen($line); $i++) {
            $c = $line[$i];

            //outLog("i = {$i} ; c=" . $c . " str_s_idx = {$str_s_idx} is_string_start = {$is_string_start} list_i = {$list_i}");
            if ($is_string_start) {
                if ('"' == $c) {
                    $r[] = substr($line, $str_s_idx, $i - $str_s_idx + 1);
                    $str_s_idx = 0;
                    $list_i = $i + 1;
                    $is_string_start = 0;
                }
            } else if ('"' == $c) {
                $str_s_idx = $i;
                $is_string_start = 1;
            } else if (in_array($c, self::$LIST_SYMBOL)) {
                if ($i - $list_i == 0) {
                    $len = $i - $list_i == 0 ? 1 : $i - $list_i;
                    $r[] = substr($line, $list_i, $len);
                } else {
                    $len = $i - $list_i == 0 ? 1 : $i - $list_i;
                    $r[] = substr($line, $list_i, $len);
                    $r[] = $c;
                }
                $list_i = $i + 1;
            } else if (" " == $c) {
                if ($i != $list_i) {
                    $r[] = substr($line, $list_i, $i - $list_i);
                }
                $list_i = $i + 1;
            }
        }

        //outLog($r);
        return $r;
    }

    function hasMoreTokens()
    {
        return $this->cur_idx < $this->total_tokens;
    }

    function nextToken()
    {
        /*
        outLog("CUR");
        outLog($this->token);
        outLog($this->tokens[$this->cur_idx]);
        outLog($this->tokens[$this->cur_idx+1]);
        */
        return $this->tokens[$this->cur_idx];
    }

    function advance()
    {
        $this->token = $this->tokens[$this->cur_idx++];
    }

    function tokenType()
    {
        if (in_array(strtolower($this->token), self::$LIST_KEYWORD)) {
            return self::$T_KEYWORD;
        } else if (in_array($this->token, self::$LIST_SYMBOL)) {
            return self::$T_SYMBOL;
        } else if (is_numeric($this->token)) {
            return self::$T_INT_CONST;
        } else if ('"' == $this->token[0] && '"' == $this->token[strlen($this->token) - 1]) {
            return self::$T_STRING_CONST;
        } else if (!is_numeric($this->token[0])) {
            return self::$T_IDENTIFIER;
        } else {
            outLog("ERROR: " . __FILE__ . ' ' . __FUNCTION__ . ' ' . __LINE__);
        }
    }

    function tokenTypeStr()
    {
        $r = '';
        $type = $this->tokenType();
        if (self::$T_KEYWORD == $type) {
            $r = 'keyword';
        } else if (self::$T_SYMBOL == $type) {
            $r = 'symbol';
        } else if (self::$T_IDENTIFIER == $type) {
            $r = 'identifier';
        } else if (self::$T_INT_CONST == $type) {
            $r = 'integerConstant';
        } else if (self::$T_STRING_CONST == $type) {
            $r = 'stringConstant';
        }
        return $r;
    }

    function keyword()
    {
        return $this->token;
    }

    function symbol()
    {
        return $this->token;
    }

    function identifier()
    {
        return $this->token;
    }

    function intVal()
    {
        return $this->token;
    }

    function stringVal()
    {
        return str_replace('"', '', $this->token);
    }


    function process()
    {
        if ($this->hasMoreTokens()) {
            $this->advance();
            while ($this->hasMoreTokens()) {
                $type = $this->tokenType();
                $this->processType($type);
                $this->advance();
            }
        }
    }

    function processType($type)
    {
        //if ("")
    }

    function isOp()
    {
        return in_array($this->token, ['+', '-', '*', '/', '&', '|', '=', '>', '<']);
    }

    function show()
    {
        outLog("tokens");
        outLog($this->tokens);
    }
}
