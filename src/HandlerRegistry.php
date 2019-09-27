<?php

namespace Nihilus\Handling;

class HandlerRegistry
{
    private static $array = [];

    public static function add(string $queryName, string $handlerName)
    {
        self::$array[$queryName] = $handlerName;
    }

    public static function get(string $queryName)
    {
        foreach (self::$array as $key => $value) {
            if ($key === $queryName) {
                return $value;
            }
        }
    }
}
