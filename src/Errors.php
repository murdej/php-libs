<?php

namespace Murdej;

class Errors
{
    public static function toException() {
        set_error_handler(function($errno, $errstr, $errfile, $errline) {
            throw new \Exception($errstr, $errno);
        });
    }
}