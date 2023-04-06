<?php
class JackAnalyzer
{
    function __construct($file)
    {
        $_files = outJackToXML($file, 'jack', 'xml');
        $_files_vm = outJackToXML($file, 'jack', 'vm');
        outLog($_files);

        foreach ($_files as $_jack => $_xml) {
            $_vm = $_files_vm[$_jack];
            $file_contents = explode(PHP_EOL, file_get_contents($_jack));
            $lines = clean($file_contents);
            $tknzr = new JackTokenizer($lines);
            $comp = new CompilationEngine($tknzr);
            if ($tknzr->hasMoreTokens()) {
                $tknzr->advance();
                $comp->compileClass();
            }
            //outLog($comp->xml);
            //outLog("write to file: {$_xml}");
            file_put_contents($_xml, join(PHP_EOL, $comp->oxml));

            //outLog("=========");
            $tknzr = new JackTokenizer($lines);
            $comp = new CompilationEngineVm($tknzr);
            if ($tknzr->hasMoreTokens()) {
                $tknzr->advance();
                $comp->compileClass();
            }
            //outLog($comp->xml);
            outLog("write to file: {$_vm}");
            //file_put_contents($_vm, join(PHP_EOL, $comp->oxml));
            //$comp->vm->close(str_replace("My", "MyVm", $_vm));
            $comp->vm->close($_vm);
        }
    }
}
