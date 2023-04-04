<?php
require_once './Helpfn.php';
require_once './JackTokenizer.php';
require_once './JackAnalyzer.php';
require_once './CompilationEngine.php';
require_once './SymbolTable.php';
require_once './VMWriter.php';
require_once './CompilationEngineVm.php';

//$file = "./10/Square/";
//$file = "./10/ArrayTest/";
//$file = "./10/ExpressionLessSquare/";
//$file = "./10/Square/Main.jack";
//$file = "./10/Square/Square.jack";
//$file = "./10/Square/SquareGame.jack";
//$file = "./10/ArrayTest/Main.jack";
//$file = "./11/Square/Main.jack";
$file = "./11/Seven/Main.jack";
$file = "./11/ConvertToBin/Main.jack";
$jack = new JackAnalyzer($file);
