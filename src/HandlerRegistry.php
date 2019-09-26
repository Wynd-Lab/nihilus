<?php

namespace Nihilus\Handling;

class HandlerRegistry
{
    private static $array = array();

    public static function add(string $queryName, string $handlerName)
    {
        HandlerRegistry::$array[$queryName] = $handlerName;
    }

    public static function get(string $queryName)
    {
        $exists = array_key_exists($queryName, HandlerRegistry::$array);

        foreach(HandlerRegistry::$array as $key=>$value)
        {
            if($key === $queryName) 
            {
                return $value;
            }
        }
    }
}