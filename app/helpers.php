<?php

if (!function_exists('dd')) {
    function dd(...$vars)
    {
        foreach ($vars as $var) {
            dump($var);
        }
        die(1);
    }
}
