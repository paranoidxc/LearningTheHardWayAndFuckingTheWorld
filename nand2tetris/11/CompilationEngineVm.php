<?php
class CompilationEngineVm
{
    public $tknzr;
    public $xml = [];
    public $oxml = [];

    public $symbol_table = NULL;

    public $vm = NULL;
    public $_map = [];
    public $is_sb = 0;
    public $sb_str = '';

    function __construct($tknzr)
    {
        $this->tknzr = $tknzr;
        $this->symbol_table = new SymbolTable();
        $this->vm = new VMWriter();
    }

    function compileClass()
    {
        $this->push("<class>");
        $this->process("class");
        $this->_map['class_name'] = $this->tknzr->token;
        $this->compileVarDec();
        $this->process("{");
        $this->compileClassVarDec();
        $this->compileSubroutineDec();
        outLog($this->tknzr->token);
        $this->process("}");
        $this->push("</class>");
    }

    function compileClassVarDec()
    {
        if (in_array($this->tknzr->token, ['static', 'field'])) {
            $this->push("<classVarDec>");
            $kind = $this->tknzr->token;
            $this->process($this->tknzr->token);

            $type = $this->tknzr->token;
            $this->process($this->tknzr->token);

            $name = $this->tknzr->token;
            $this->process($this->tknzr->token);

            $this->symbol_table->define($name, $type, $kind);
            if ("," == $this->tknzr->token) {
                $this->process(",");
                $name = $this->tknzr->token;
                $this->process($this->tknzr->token);
                $this->symbol_table->define($name, $type, $kind);
            }
            $this->process(";");
            $this->push("</classVarDec>");

            $this->compileClassVarDec();
        }
    }


    public $methods = [];
    function compileSubroutineDec()
    {
        while (in_array($this->tknzr->token, ['constructor', 'function', 'method'])) {
            outLog("Start startSubroutine");
            $fun_type = $this->tknzr->token;
            $this->symbol_table->startSubroutine();

            $this->push("<subroutineDec>");

            $this->while_idx = 0;
            $this->if_idx = 0;

            $this->process($this->tknzr->token);

            $this->compileVarDec();

            $fun = $this->tknzr->token;
            $this->compileVarDec();

            $nArgs = $this->compileParameterList();

            $this->push("<subroutineBody>");
            $this->process("{");
            $this->compileVarDec2();

            $r = [];
            if ($fun_type == "method") {
                //$nArgs += 1;
                $nArgs = $this->symbol_table->varCount('local');
                $r[] = "push argument 0";
                $r[] = "pop pointer 0";
                $this->methods[] = $fun;
            } elseif ($fun_type == "function") {
                $nArgs = $this->symbol_table->varCount('local');
                //outLog("nARgs");
                //outLog($nArgs);
            } elseif ($fun_type == "constructor") {
                $nArgs = 0;
                $nField = $this->symbol_table->varCount('field');
                $r[] = "push constant {$nField}";
                $r[] = "call Memory.alloc 1";
                $r[] = "pop pointer 0";
            }

            $fun_type = "function";
            $this->vm->write("{$fun_type} {$this->_map['class_name']}.{$fun} {$nArgs}");
            $this->vm->write($r);


            $this->compileStatements();
            $this->process("}");
            $this->push("</subroutineBody>");
            $this->push("</subroutineDec>");
        }

        outLog($this->symbol_table->out());
    }

    function compileSubroutine()
    {
        $fun = $this->tknzr->token;
        $this->compileVarDec();
        $plus_nargs = 0;
        if ('.' == $this->tknzr->token) {
            //$this->symbol_table->out();

            $kind = $this->symbol_table->kindOf($fun);
            $type = $this->symbol_table->typeOf($fun);
            $idx = $this->symbol_table->indexOf($fun);

            //outLog("kind = {$kind}; type = {$type}; index = {$idx}");

            $this->process(".");
            if ($this->is_para && $type) {
                $fun = $type;
                $plus_nargs = 1;
            }
            $fun .= "." . $this->tknzr->token;
            $this->compileVarDec();

            if ($this->is_para) {
                $this->push("push {$kind} {$idx}");
            }
        } else {
            //outLog("METHOD = ");
            //outLog($this->methods);
            //if (in_array($fun, $this->methods)) {
            $fun = $this->_map['class_name'] . "." . $fun;
            $this->vm->write("push pointer 0");
            $plus_nargs = 1;
            //}
        }
        $nArgs = $this->compileExpressionList();
        $nArgs += $plus_nargs;

        return [$fun, $nArgs];
    }

    function compileParameterList()
    {
        $nArgs = 0;
        $this->process("(");
        $this->push("<parameterList>");
        if (")" != $this->tknzr->token) {

            $type = $this->tknzr->token;
            $this->process($this->tknzr->token);

            $name = $this->tknzr->token;
            $this->process($this->tknzr->token);

            $this->symbol_table->define($name, $type, "argument");

            $nArgs += 1;
            while ("," == $this->tknzr->token) {
                $this->process(",");

                $type = $this->tknzr->token;
                $this->process($this->tknzr->token);

                $name = $this->tknzr->token;
                $this->process($this->tknzr->token);

                $this->symbol_table->define($name, $type, "argument");
                $nArgs += 1;
            }
        }
        $this->push("</parameterList>");
        $this->process(")");

        return $nArgs;
    }

    function compileVarDec2()
    {
        while ("var" == $this->tknzr->token) {
            //$this->is_sb = 1;
            //$this->sb_str = "var";
            $this->push("<varDec>");

            $kind = $this->tknzr->token;
            $this->process($this->tknzr->token);

            $type = $this->tknzr->token;
            $this->process($this->tknzr->token);

            $name = $this->tknzr->token;
            $this->process($this->tknzr->token);

            $this->symbol_table->define($name, $type, $kind);

            while ("," == $this->tknzr->token) {
                $this->process(",");
                $name = $this->tknzr->token;
                $this->process($this->tknzr->token);
                $this->symbol_table->define($name, $type, $kind);
            }

            $this->process(";");
            $this->push("</varDec>");
        }
    }

    function compileVarDec()
    {
        if (JackTokenizer::$T_KEYWORD == $this->tknzr->tokenType()) {
            if (in_array($this->tknzr->token, [
                'var', 'argument',
                'static', 'field', 'class', 'subroutine'
            ])) {
                $this->is_sb = 1;
                $this->sb_str = $this->tknzr->tokenTypeStr();
            } else {
                $this->is_sb = 0;
            }
            //$this->symbol_table->define($name $type, $kinde);
            $this->process($this->tknzr->token);
        } else {
            if (1 == $this->is_sb) {
                $this->push("<{$this->sb_str}> " . $this->tknzr->token . " </{$this->sb_str}>");
            } else {
                if ($this->is_array) {
                    if ($this->symbol_table->kindOf($this->tknzr->token)) {
                        $kind = $this->symbol_table->kindOf($this->tknzr->token);
                        $idx = $this->symbol_table->indexOf($this->tknzr->token);
                        array_unshift($this->array_tmp, "push {$kind} {$idx}");
                        outLog("ArrayTmp");
                        outLog($this->tknzr->token);
                        outLog($this->array_tmp);
                    }
                } else if ($this->is_para) {
                    if ($this->symbol_table->kindOf($this->tknzr->token)) {
                        $kind = $this->symbol_table->kindOf($this->tknzr->token);
                        $idx = $this->symbol_table->indexOf($this->tknzr->token);
                        $this->vm->write("push {$kind} {$idx}");
                    }
                }

                $this->push("<identifier> " . $this->tknzr->token . " </identifier>");
            }
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
        $this->is_para = 1;
        list($fun, $nArgs) = $this->compileSubroutine();
        $this->vm->writeCall($fun, $nArgs, $this->is_para);
        $this->is_para = 0;
        $this->process(";");
        $this->push("</doStatement>");
        $this->vm->write("pop temp 0");
    }

    public $is_array = 0;
    public $array_tmp = [];
    function compileLet()
    {
        outLog("Fun:" . __FUNCTION__);
        $this->push("<letStatement>");
        $this->process("let");
        $name = $this->tknzr->token;
        if ('[' != $this->tknzr->nextToken()) {
            $this->compileVarDec();
        } else {
            $this->is_array = 1;
            $this->compileVarDec();
        }

        $is_array = FALSE;
        if ("[" == $this->tknzr->token) {
            $is_array = TRUE;
            //$this->compileVarDec();
            $this->process("[");
            $this->compileExpression();
            $this->process("]");

            if ($this->is_array) {
                $this->vm->write($this->array_tmp);
                $this->vm->write("add");
                $this->is_array = 0;
                $this->array_tmp = [];
            }
        }

        $this->process("=");
        $this->is_para = 1;
        outLog("IS_PARA {$this->is_para}");
        $this->compileExpression();
        $this->is_para = 0;
        $this->process(";");
        $this->push("</letStatement>");

        $this->symbol_table->out();

        $var_kind = $this->symbol_table->kindOf($name);
        $var_idx = $this->symbol_table->indexOf($name);

        outLog("name={$name}; kind = {$var_kind}; idx = {$var_idx}");
        if ($is_array) {
            $var_kind = "temp";
        }

        $this->vm->write("pop {$var_kind} {$var_idx}");
        if ($is_array) {
            $this->vm->write("pop pointer 1");
            $this->vm->write("push temp 0");
            $this->vm->write("pop that 0");
        }
    }

    public $while_idx = 0;
    function compileWhile()
    {
        $idx = $this->while_idx;
        $this->while_idx += 1;
        $this->vm->write("label WHILE_EXP{$idx}");
        $this->push("<whileStatement>");
        $this->process("while");
        $this->process("(");
        $this->is_para = 1;
        $this->compileExpression();
        $this->vm->write("not");
        $this->vm->write("if-goto WHILE_END{$idx}");
        $this->is_para = 0;
        $this->process(")");
        $this->process("{");
        $this->compileStatements();

        $this->vm->write("goto WHILE_EXP{$idx}");
        $this->vm->write("label WHILE_END{$idx}");

        $this->process("}");
        $this->push("</whileStatement>");
    }

    function compileReturn()
    {
        $r = [];
        $this->push("<reurnStatement>");
        $this->process("return");
        if (";" != $this->tknzr->token) {
            $global_is_para = $this->is_para;
            $this->is_para = 1;
            $this->compileExpression();
            $this->is_para = $global_is_para;
        } else {
            $r[] = "push constant 0";
        }
        $this->process(";");
        $this->push("</returnStatement>");

        $r[] = "return";
        $this->vm->write($r);
    }

    public $if_idx = 0;
    function compileIf()
    {
        $if_idx = $this->if_idx;
        $this->if_idx += 1;

        $this->push("<ifStatement>");
        $this->process("if");
        $this->process("(");
        $this->is_para = 1;
        $this->compileExpression();
        $this->is_para = 0;
        $this->process(")");

        $this->vm->write("if-goto IF_TRUE{$if_idx}");
        $this->vm->write("goto IF_FALSE{$if_idx}");
        $this->process("{");
        $this->vm->write("label IF_TRUE{$if_idx}");
        $this->compileStatements();
        //$this->vm->write("label IF_END{$if_idx}");
        $this->process("}");

        if ("else" == $this->tknzr->token) {
            $this->process("else");
            $this->process("{");
            $this->vm->write("goto IF_END{$if_idx}");
            $this->vm->write("label IF_FALSE{$if_idx}");
            $this->compileStatements();
            $this->vm->write("label IF_END{$if_idx}");
            $this->process("}");
        } else {
            $this->vm->write("label IF_FALSE{$if_idx}");
        }
        $this->push("</ifStatement>");
    }

    function compileExpression()
    {
        $this->push("<expression>");
        $this->compileTerm();
        $this->push("</expression>");
    }

    function compileTerm($is_fun = false)
    {
        $this->push("<term>");

        $fun = '';
        $nArgs = 0;
        if (in_array($this->tknzr->token, ['-', '~'])) {
            $is_neg = 0;
            $is_not = 0;
            if ('-' == $this->tknzr->token) {
                $is_neg = 1;
            }
            if ('~' == $this->tknzr->token) {
                $is_not = 1;
            }
            $this->process($this->tknzr->token);
            $this->compileTerm();
            if ($is_neg) {
                $this->vm->write("neg");
            }
            if ($is_not) {
                $this->vm->write("not");
            }
        } else if (JackTokenizer::$T_INT_CONST == $this->tknzr->tokenType()) {
            $this->vm->writeLabel("constant {$this->tknzr->token}");
            $this->process($this->tknzr->token);
        } else if (JackTokenizer::$T_STRING_CONST == $this->tknzr->tokenType()) {
            $this->process($this->tknzr->token);
        } else if (JackTokenizer::$T_KEYWORD == $this->tknzr->tokenType()) {
            $this->process($this->tknzr->token);
        } else if (JackTokenizer::$T_IDENTIFIER == $this->tknzr->tokenType()) {
            //outLog("token = ".$this->tknzr->token);
            //outLog('is para = '.$this->is_para);
            if ("[" == $this->tknzr->nextToken()) {
                $this->is_array = 1;
                outLog("NOW = " . $this->tknzr->token);
                outLog("NEXT = " . $this->tknzr->nextToken());
                $this->compileVarDec();
                $this->process("[");
                $this->compileExpression();
                $this->process("]");

                outLog($this->array_tmp);

                $this->vm->write($this->array_tmp);
                $this->vm->write("add");

                $this->vm->write("pop pointer 1");
                $this->vm->write("push that 0");
                $this->is_array = 0;
            } else if ("." == $this->tknzr->nextToken()) {
                $fun = $this->tknzr->token;
                $is_fun = true;
                $this->compileVarDec();
                $this->process(".");
                if ($is_fun) {
                    $fun .= "." . $this->tknzr->token;
                }
                $this->compileVarDec();
            } else {
                $this->compileVarDec();
            }

            if ("(" == $this->tknzr->token) {
                $nArgs = $this->compileExpressionList();
            }

            if ($is_fun) {
                outLog("is FUN = {$fun}, nArgs = {$nArgs}; global_is_para = {$this->is_para}");
                $this->vm->writeCall($fun, $nArgs, $this->is_para);
            }
        } else if (JackTokenizer::$T_SYMBOL == $this->tknzr->tokenType()) {
            $this->process("(");
            $this->compileExpression();
            $this->process(")");
        }

        $this->push("</term>");

        while ($this->tknzr->isOp()) {
            $is_para = 0;
            $n = "";
            $nG = "2";
            if (in_array($this->tknzr->token, ['*'])) {
                $n = "Math.multiply";
                $nG = "2";
                $is_para = 1;
            } else if (in_array($this->tknzr->token, ['/'])) {
                $n = "Math.divide";
                $nG = "2";
                $is_para = 1;
            } else if (in_array($this->tknzr->token, ['+'])) {
                $n = "add";
                $nG = "";
            } else if (in_array($this->tknzr->token, ['-'])) {
                $n = "sub";
                $nG = "";
            } else if (in_array($this->tknzr->token, ['='])) {
                $n = "eq";
                $nG = "";
            } else if (in_array($this->tknzr->token, ['<'])) {
                $n = "lt";
                $nG = "";
            } else if (in_array($this->tknzr->token, ['>'])) {
                $n = "gt";
                $nG = "";
            } else if (in_array($this->tknzr->token, ['&'])) {
                $n = "and";
                $nG = "";
            } else if (in_array($this->tknzr->token, ['|'])) {
                $n = "or";
                $nG = "";
            }
            $this->process($this->tknzr->token);
            $this->compileTerm();

            if ($n) {
                $this->vm->writeCall($n, $nG, $is_para);
            }
        }

        if ($is_fun) {
            return [$fun, $nArgs];
        }
    }

    public $is_para = 0;
    function compileExpressionList()
    {
        $nArgs = 0;
        $this->process("(");
        $global_is_para = $this->is_para;
        $this->is_para = 1;
        $this->push("<expressionList>");
        if (")" != $this->tknzr->token) {
            $this->compileExpression();
            $nArgs += 1;
            while ("," == $this->tknzr->token) {
                $this->process(",");
                $this->compileExpression();
                $nArgs += 1;
            }
        }
        $this->push("</expressionList>");
        $this->process(")");
        $this->is_para = $global_is_para;
        return $nArgs;
    }

    function process($str)
    {
        //outLog("token={$this->tknzr->token} AND str={$str}");
        if (0 == strlen($str)) {
            $str = $this->tknzr->token;
        }
        if ($this->tknzr->token == $str) {
            $this->printXMLToken($str);
        } else {
            outLog("syntax error: token={$this->tknzr->token}  AND guess str={$str}");
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
        if ($this->is_sb && JackTokenizer::$T_IDENTIFIER == $this->tknzr->tokenType()) {
            $type_str = $this->sb_str;
        }

        if ("true" == $token) {
            $this->vm->write("push constant 0");
            $this->vm->write("not");
        } else if ("false" == $token) {
            $this->vm->write("push constant 0");
        } else if ("this" == $token) {
            $this->vm->write("push pointer 0");
        } else if ($this->is_para) {
            //outLog("++++++++++++++");
            //outLog($this->tknzr->token);
            //outLog($this->symbol_table->kindOf($this->tknzr->token));
            if ($this->symbol_table->kindOf($this->tknzr->token)) {
                if ('argument' == $this->symbol_table->kindOf($this->tknzr->token)) {
                    $idx = $this->symbol_table->indexOf($this->tknzr->token);
                    $this->vm->write("push argument {$idx}");
                    return;
                }
            }
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
