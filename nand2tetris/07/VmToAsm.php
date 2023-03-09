<?php
function outLog($str)
{
    print_r($str);
    print_r("<BR>");
}

function clean($lines = [])
{
    $r = [];
    foreach ($lines as $l) {
        $l = trim($l);
        if ($l[0] == "/" && $l[1] == "/") {
            continue;
        }
        list($l, $trash) = explode("//", $l);
        if (strlen($l)) {
            $r[] = $l;
        }
    }
    return $r;
}

function outLocalFilePathOfAsm($f)
{
    $pathinfo = pathinfo($f);
    return $pathinfo['dirname'] . DIRECTORY_SEPARATOR . $pathinfo['filename'] . '.asm';
}

require_once './Parse.php';
require_once './CodeWrite.php';

$files = [
    "./07/StackArithmetic/SimpleAdd/SimpleAdd.vm",
    "./07/StackArithmetic/StackTest/StackTest.vm",
    "./07/MemoryAccess/BasicTest/BasicTest.vm",
    "./07/MemoryAccess/PointerTest/PointerTest.vm",
    "./07/MemoryAccess/StaticTest/StaticTest.vm",
];
foreach ($files as $f) {
    $out_asm_f = outLocalFilePathOfAsm($f);
    //outLog($out_asm_f);

    $file_contents = explode(PHP_EOL, file_get_contents($f));
    outLog("<pre>");
    //outLog($file_contents);
    $lines = clean($file_contents);
    outLog($lines);

    $parse = new Parse($lines);
    $code = new CodeWrite();
    $code->setFileName($out_asm_f);

    while ($parse->hasMoreCommands()) {
        $parse->advance();
        $type = $parse->commandType();
        if (in_array($type, [Parse::$C_POP, Parse::$C_PUSH])) {
            //outLog("call writePushPop");
            $code->writePushPop($type, $parse->arg1(), $parse->arg2());
        } else if ($type == Parse::$C_ARITHMETIC) {
            $code->writeArithmetic($parse->arg1());
        }
    }
    outLog($code->close());
}
