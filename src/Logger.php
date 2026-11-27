<?php
namespace App;

class Logger
{
    public static function info(string $msg): void    { self::log('INFO', $msg); }
    public static function warning(string $msg): void { self::log('WARNING', $msg); }
    public static function error(string $msg): void   { self::log('ERROR', $msg); }

    private static function log(string $nivel, string $msg): void
    {
        file_put_contents('php://stderr', sprintf("[%s] %s %s\n", date('c'), $nivel, $msg));
    }
}