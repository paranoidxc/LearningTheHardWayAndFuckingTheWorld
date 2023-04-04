<?php
class CompilationEngine
{
    public $tknzr;
    public $xml = [];
    public $oxml = [];

    function __construct($tknzr)
    {
        $this->tknzr = $tknzr;
    }

    function compileClass()
    {
        $this->push("<class>");
        $this->process("class");
        $this->compileVarDec();
        $this->process("{");
        $this->compileClassVarDec();
        $this->compileSubroutineDec();
        $this->process("}");
        $this->push("</class>");
    }

    function compileClassVarDec()
    {
        if (in_array($this->tknzr->token, ['static', 'field'])) {
            $this->push("<classVarDec>");
            $this->process($this->tknzr->token);
            $this->process($this->tknzr->token);
            $this->process($this->tknzr->token);
            if ("," == $this->tknzr->token) {
                $this->process(",");
                $this->process($this->tknzr->token);
            }
            $this->process(";");
            $this->push("</classVarDec>");

            $this->compileClassVarDec();
        }
    }

    function compileSubroutineDec()
    {
        while (in_array($this->tknzr->token, ['constructor', 'function', 'method'])) {
            $this->push("<subroutineDec>");

            $this->process($this->tknzr->token);

            $this->compileVarDec();
            $this->compileVarDec();

            $this->compileParameterList();

            $this->push("<subroutineBody>");
            $this->process("{");
            $this->compileVarDec2();
            $this->compileStatements();
            $this->process("}");
            $this->push("</subroutineBody>");
            $this->push("</subroutineDec>");
        }
    }

    function compileSubroutine()
    {
        $this->compileVarDec();
        if ('.' == $this->tknzr->token) {
            $this->process(".");
            $this->compileVarDec();
        }
        $this->compileExpressionList();
    }

    function compileParameterList()
    {
        $this->process("(");
        $this->push("<parameterList>");
        if (")" != $this->tknzr->token) {
            $this->process($this->tknzr->token);
            $this->process($this->tknzr->token);
            while ("," == $this->tknzr->token) {
                $this->process(",");
                $this->process($this->tknzr->token);
                $this->process($this->tknzr->token);
            }
        }
        $this->push("</parameterList>");
        $this->process(")");
    }

    function compileVarDec2()
    {
        while ("var" == $this->tknzr->token) {
            $this->push("<varDec>");
            $this->process($this->tknzr->token);
            $this->process($this->tknzr->token);
            $this->process($this->tknzr->token);

            while ("," == $this->tknzr->token) {
                $this->process(",");
                $this->process($this->tknzr->token);
            }

            $this->process(";");
            $this->push("</varDec>");
        }
    }

    function compileVarDec()
    {
        if (JackTokenizer::$T_KEYWORD == $this->tknzr->tokenType()) {
            $this->process($this->tknzr->token);
        } else {
            $this->push("<identifier> " . $this->tknzr->token . " </identifier>");
            $this->tknzr->advance();
        }
    }

    function compileStatements()
    {
        $this->push("<statements>");
        while (in_array($this->tknzr->token, [
            "let",
            "if",
            "while",
            "do",
            "return",
        ])) {
            if ("let" == $this->tknzr->token) {
                $this->compileLet();
            } else if ("if" == $this->tknzr->token) {
                $this->compileIf();
            } else if ("while" == $this->tknzr->token) {
                $this->compileWhile();
            } else if ("do" == $this->tknzr->token) {
                $this->compileDo();
            } else if ("return" == $this->tknzr->token) {
                $this->compileReturn();
            }
        }
        $this->push("</statements>");
    }

    function compileDo()
    {
        $this->push("<doStatement>");
        $this->process("do");
        $this->compileSubroutine();
        $this->process(";");
        $this->push("</doStatement>");
    }

    function compileLet()
    {
        $this->push("<letStatement>");
        $this->process("let");
        $this->compileVarDec();
        if ("[" == $this->tknzr->token) {
            $this->process("[");
            $this->compileExpression();
            $this->process("]");
        }
        $this->process("=");
        $this->compileExpression();
        $this->process(";");
        $this->push("</letStatement>");
    }

    function compileWhile()
    {
        $this->push("<whileStatement>");
        $this->process("while");
        $this->process("(");
        $this->compileExpression();
        $this->process(")");
        $this->process("{");
        $this->compileStatements();
        $this->process("}");
        $this->push("</whileStatement>");
    }

    function compileReturn()
    {
        $this->push("<returnStatement>");
        $this->process("return");
        if (";" != $this->tknzr->token) {
            $this->compileExpression();
        }
        $this->process(";");
        $this->push("</returnStatement>");
    }

    function compileIf()
    {
        $this->push("<ifStatement>");
        $this->process("if");
        $this->process("(");
        $this->compileExpression();
        $this->process(")");
        $this->process("{");
        $this->compileStatements();
        $this->process("}");

        if ("else" == $this->tknzr->token) {
            $this->process("else");
            $this->process("{");
            $this->compileStatements();
            $this->process("}");
        }
        $this->push("</ifStatement>");
    }

    function compileExpression()
    {
        $this->push("<expression>");
        $this->compileTerm();
        $this->push("</expression>");
    }

    function compileTerm()
    {
        $this->push("<term>");
        //outLog($this->tknzr->token);
        //outLog($this->tknzr->tokenType());
        //outLog($this->tknzr->tokenTypeStr());

        if (in_array($this->tknzr->token, ['-', '~'])) {
            $this->process($this->tknzr->token);
            $this->compileTerm();
        } else if (JackTokenizer::$T_INT_CONST == $this->tknzr->tokenType()) {
            $this->process($this->tknzr->token);
        } else if (JackTokenizer::$T_STRING_CONST == $this->tknzr->tokenType()) {
            $this->process($this->tknzr->token);
        } else if (JackTokenizer::$T_KEYWORD == $this->tknzr->tokenType()) {
            $this->process($this->tknzr->token);
        } else if (JackTokenizer::$T_IDENTIFIER == $this->tknzr->tokenType()) {
            if ("." == $this->tknzr->nextToken()) {
                $this->compileVarDec();
                $this->process(".");
                $this->compileVarDec();
            } else if ("[" == $this->tknzr->nextToken()) {
                $this->compileVarDec();
                $this->process("[");
                $this->compileExpression();
                $this->process("]");
                //echo "nTERM exit";
                //                exit;
            } else {
                $this->compileVarDec();
            }

            if ("(" == $this->tknzr->token) {
                $this->compileExpressionList();
            }
        } else if (JackTokenizer::$T_SYMBOL == $this->tknzr->tokenType()) {
            $this->process("(");
            $this->compileExpression();
            $this->process(")");
        }
        $this->push("</term>");

        while ($this->tknzr->isOp()) {
            $this->process($this->tknzr->token);
            $this->compileTerm();
        }
    }

    function compileExpressionList()
    {
        $this->process("(");
        $this->push("<expressionList>");
        if (")" != $this->tknzr->token) {
            $this->compileExpression();
            while ("," == $this->tknzr->token) {
                $this->process(",");
                $this->compileExpression();
            }
        }
        $this->push("</expressionList>");
        $this->process(")");
    }

    function process($str)
    {
        //outLog("token={$this->tknzr->token} | str={$str}");
        if (0 == strlen($str)) {
            $str = $this->tknzr->token;
        }
        if ($this->tknzr->token == $str) {
            $this->printXMLToken($str);
        } else {
            outLog("syntax error: token={$this->tknzr->token} | guess str={$str}");
        }
        $this->tknzr->advance();
    }

    function printXMLToken($token)
    {
        //outLog($token);
        $type_str = $this->tknzr->tokenTypeStr();
        if ($token == "<") {
            $token = "&lt;";
        } else if ($token == ">") {
            $token = "&gt;";
        } else if ($token == "&") {
            $token = "&amp;";
        }

        if (JackTokenizer::$T_STRING_CONST == $this->tknzr->tokenType()) {
            $token = str_replace('"', '', $token);
        }
        $this->push("<{$type_str}> {$token} </{$type_str}>");
    }

    function push($str = '')
    {
        if (strlen($str)) {
            $this->xml[] = htmlspecialchars($str);
            $this->oxml[] = $str;
        }
    }
}
