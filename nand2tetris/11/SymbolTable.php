<?php
class SymbolTable
{
    public $sb = [];
    public $idx = -1;

    public $level = 0;
    public static $SB_CLASS = 0;
    public static $SB_SUB = 1;

    public $class_sb = [];
    public $subroutine_sb = [];

    public static $NAME = 0;
    public static $TYPE = 1;
    public static $KIND = 2;
    public static $INDEX = 3;

    function constructor()
    {
    }

    function startSubroutine()
    {
        $this->idx = -1;
        $this->level = SymbolTable::$SB_SUB;
        $this->subroutine_sb = [];
    }

    function define($name, $type, $kind)
    {
        if ($kind == 'var') {
            $kind = "local";
        }
        $count = $this->varCount($kind);

        outLog("Fun: " . __FUNCTION__ . " n={$name} t=$type k=$kind");
        outLog("exist count = {$count}");

        if (!strlen($count)) {
            $count = -1;
        } else {
            $count -= 1;
        }
        if (in_array($kind, ['static', 'field'])) {
            $this->class_sb[] = [$name, $type, $kind, $count + 1];
        } else {
            $this->subroutine_sb[] = [$name, $type, $kind, $count + 1];
        }
        //outLog("AFTER DEFINE");
        //$this->out();
    }

    function varCount($kind)
    {
        $r = 0;
        $sb = $this->subroutine_sb;
        if (in_array($kind, ['static', 'field'])) {
            $sb = $this->class_sb;
        }

        //outLog("VARCOUNT");
        //$this->out();
        //outLog($kind);
        foreach ($sb as $row) {
            if ($row[self::$KIND] == $kind) {
                $r += 1;
            }
        }

        return $r;
    }

    function kindOf($name)
    {

        $r =  $this->_rowOfName($name, self::$KIND);
        if ($r == "field") {
            $r = "this";
        }
        return $r;
    }

    function typeOf($name)
    {
        return $this->_rowOfName($name, self::$TYPE);
    }

    function indexOf($name)
    {
        return $this->_rowOfName($name, self::$INDEX);
    }

    function _rowOfName($name, $col)
    {
        $sb = $this->subroutine_sb;;
        if (self::$SB_CLASS == $this->level) {
            //$sb = $this->class_sb;
        }
        //outLog("ROW OF name={$name} col={$col}");
        //outLog($sb);
        $r = '';
        foreach ($sb as $row) {
            if ($row[self::$NAME] == $name) {
                $r = $row[$col];
            }
        }


        if (!$r) {
            $sb = $this->class_sb;
            foreach ($sb as $row) {
                if ($row[self::$NAME] == $name) {
                    $r = $row[$col];
                }
            }
        }

        return $r;
    }

    function _sb($kind)
    {
        $r = $this->subroutine_sb;
        if (in_array($kind, ['static', 'field'])) {
            $r = $this->class_sb;
        }

        return $r;
    }

    function out()
    {
        outLog("CLASS SymbolTable");
        outLog($this->class_sb);
        outLog("SUBROUT SymbolTable");
        outLog($this->subroutine_sb);
    }
}
