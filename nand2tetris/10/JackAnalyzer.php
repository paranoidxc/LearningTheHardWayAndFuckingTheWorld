<?php
class JackAnalyzer
{
    function __construct($file)
    {
        $_files = outJackToXML($file, 'jack', 'xml');
        outLog($_files);
        foreach ($_files as $_jack => $_vm) {
            $file_contents = explode(PHP_EOL, file_get_contents($_jack));
            $lines = clean($file_contents);
            $tknzr = new JackTokenizer($lines);
            $comp = new CompilationEngine($tknzr);
            if ($tknzr->hasMoreTokens()) {
                $tknzr->advance();
                $comp->compileClass();
            }
            outLog($comp->xml);
            outLog("write to file: {$_vm}");
            file_put_contents($_vm, join(PHP_EOL, $comp->oxml));
        }
    }
}
