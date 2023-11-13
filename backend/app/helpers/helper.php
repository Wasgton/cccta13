<?php

use Symfony\Component\VarDumper\VarDumper;

if (! function_exists('dd')) {
    function dd(...$vars)
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: *');
        header('Access-Control-Allow-Headers: *');
        http_response_code(500);

        foreach ($vars as $v) {
            VarDumper::dump($v);
        }

        die(1);
    }
}
