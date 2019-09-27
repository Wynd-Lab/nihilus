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
        $exists = array_key_exists($queryName, self::$array);

        foreach (self::$array as $key => $value) {
            if ($key === $queryName) {
                return $value;
            }
        }
    }
}
