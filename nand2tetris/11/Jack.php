<?php
require_once './Helpfn.php';
require_once './JackTokenizer.php';
require_once './JackAnalyzer.php';
require_once './CompilationEngine.php';
require_once './SymbolTable.php';
require_once './VMWriter.php';
require_once './CompilationEngineVm.php';

$files = [
];

$files = [
    "./11/Seven/Main.jack",
    "./11/ConvertToBin/Main.jack",
    "./11/Seven/Main.jack",
    "./11/Square/Main.jack",
    "./11/Square/SquareGame.jack",
    "./11/Square/Square.jack",
];

foreach ($files as $file) {
    new JackAnalyzer($file);
}
