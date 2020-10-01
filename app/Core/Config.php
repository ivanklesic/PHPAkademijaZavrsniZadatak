<?php


namespace App\Core;


class Config
{
    public static function get($key)
    {
        $config = include_once BP . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'config.php';
        return $config[$key] ?? null;
    }

    public static function prependToURL()
    {

    }
}