<?php

use function PHPSTORM_META\map;

function outLog($str)
{
    static $is_pre = false;
    if (!$is_pre) {
        $is_pre = TRUE;
        print_r("<pre>");
    }
    print_r($str);
    print_r("<BR>");
}

function outJackToXML($f, $source, $target)
{
    $r = [];

    $files = [$f];
    if (is_dir($f)) {
        $files = listSepFile($f, $source);
    }

    foreach ($files as $f) {
        $p = pathinfo($f);
        $r[$f] = join(DIRECTORY_SEPARATOR, [
            $p['dirname'],
            $p['filename'] . "My.{$target}",
        ]);
    }

    return $r;
}

function outLocalFilePathOfSep($f, $target, $source)
{
    $pathinfo = pathinfo($f);
    $files = [$f];
    $r[] = $pathinfo['dirname'];
    if (is_dir($f)) {
        $r[] = $pathinfo['basename'];
        $files = listSepFile($f, $source);
    }
    $r[] = $pathinfo['filename'] . "My.{$target}";
    return [join(DIRECTORY_SEPARATOR, $r), $files];
}

function clean($lines = [])
{
    $r = [];
    $is_mul = 0;

    foreach ($lines as $l) {
        //outLog("BEF:".$l);
        $l = trim($l);
        if ($is_mul) {
            if ($l[0] == "*" && $l[1] == "/") {
                $is_mul = 0;
            }
            continue;
        }

        if ($l[0] == "/" && $l[1] == "/") {
            continue;
        }
        if ($l[0] == "/" && $l[1] == "*") {
            if ($l[strlen($l) - 2] == '*' && $l[strlen($l) - 1] == '/') {
                continue;
            }
            $is_mul = 1;
            continue;
        }
        list($l, $trash) = explode("//", $l);
        if (strlen($l)) {
            $r[] = $l;
        }

        //outLog("AFT:".$l);
    }
    return $r;
}

function listSepFile($dir, $sep = '')
{
    $r = [];
    $p = pathinfo($dir);

    if ($hd = opendir($dir)) {
        while (($file = readdir($hd)) !== FALSE) {
            if (strpos($file, $sep) !== FALSE) {
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
