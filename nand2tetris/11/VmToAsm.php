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

function listVmFile($dir)
{
    $r = [];
    $p = pathinfo($dir);

    if ($hd = opendir($dir)) {
        while (($file = readdir($hd)) !== FALSE) {
            if (strpos($file, ".vm") !== FALSE) {
                $f = join(
                    DIRECTORY_SEPARATOR,
                    [
                        $p['dirname'],
                        $p['basename'],
                        $file
                    ]
                );
                if (is_file($f)) {
                    $r[] = $f;
                }
            }
        }
    }

    return $r;
}

function outLocalFilePathOfAsm($f)
{
    $pathinfo = pathinfo($f);
    $files = [$f];
    $r[] = $pathinfo['dirname'];
    if (is_dir($f)) {
        $r[] = $pathinfo['basename'];
        $files = listVmFile($f);
    }
    $r[] = $pathinfo['filename'] . '.asm';
    return [join(DIRECTORY_SEPARATOR, $r), $files];
}

require_once './Parse.php';
require_once './CodeWrite.php';

$files = [
    /*
    "./07/StackArithmetic/SimpleAdd/SimpleAdd.vm",
    "./07/StackArithmetic/StackTest/StackTest.vm",
    "./07/MemoryAccess/BasicTest/BasicTest.vm",
    "./07/MemoryAccess/PointerTest/PointerTest.vm",
    "./07/MemoryAccess/StaticTest/StaticTest.vm",
    */
    //"./08/ProgramFlow/BasicLoop/BasicLoop.vm",
    //"./08/ProgramFlow/FibonacciSeries/FibonacciSeries.vm",
    //"./08/FunctionCalls/SimpleFunction/SimpleFunction.vm",
    //"./08/FunctionCalls/FibonacciElement/",
    "./08/FunctionCalls/StaticsTest/",
];


foreach ($files as $f) {
    list($out_asm_f, $_files) = outLocalFilePathOfAsm($f);

    $code = new CodeWrite();
    $code->setFileName($out_asm_f);

    foreach ($_files as $_f) {
        $file_contents = explode(PHP_EOL, file_get_contents($_f));
        $lines = clean($file_contents);
        outLog("<pre>");
        outLog($lines);
        $parse = new Parse($lines);

        $p = pathinfo($_f);
        $code->setVmFileName($p['filename']);
        while ($parse->hasMoreCommands()) {
            $parse->advance();
            $type = $parse->commandType();
            if (in_array($type, [Parse::$C_POP, Parse::$C_PUSH])) {
                $code->writePushPop($type, $parse->arg1(), $parse->arg2());
            } else if ($type == Parse::$C_ARITHMETIC) {
                $code->writeArithmetic($parse->arg1());
            } else if ($type == Parse::$C_LABEL) {
                $code->writeLabel($parse->arg1());
            } else if ($type == Parse::$C_GOTO) {
                $code->writeGoto($parse->arg1());
            } else if ($type == Parse::$C_IF) {
                $code->writeIf($parse->arg1());
            } else if ($type == Parse::$C_RETURN) {
                $code->writeReturn();
            } else if ($type == Parse::$C_CALL) {
                $code->writeCall($parse->arg1(), $parse->arg2());
            } else if ($type == Parse::$C_FUNCTION) {
                $code->writeFunction($parse->arg1(), $parse->arg2());
            } else {
                outLog("NOT IMP " . $parse->getLineContent());
            }
        }
    }
    outLog($code->close());
}
