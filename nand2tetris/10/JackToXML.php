<?php
require_once './Helpfn.php';
require_once './JackTokenizer.php';
require_once './JackAnalyzer.php';
require_once './CompilationEngine.php';

$file = "./10/Square/";
$file = "./10/Square/Main.jack";
$file = "./10/Square/Square.jack";
//$file = "./10/Square/if.jack";
$jack = new JackAnalyzer($file);
