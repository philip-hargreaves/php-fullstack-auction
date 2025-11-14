<?php
namespace infrastructure;

class DIContainer {
    private static array $registry = [];

    public static function bind(string $key, $value) : void
    {
        static::$registry[$key] = $value;
    }

    public static function get(string $key)
    {

        if (!array_key_exists($key, static::$registry)) {
//            throw new Exception("No service is bound with the key: {$key}");
        }

        return static::$registry[$key];
    }
}