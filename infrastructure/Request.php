<?php

class Request
{
    // Retrieve and sanitise variable from $_GET or return default value
    public static function get($key, $default = null)
    {
        if (isset($_GET[$key])) {
            // Retrieve and sanitise the input by stripping HTML tags
            return htmlspecialchars(strip_tags($_GET[$key]));
        }
        return $default;
    }

    // Retrieve and sanitise variable from $_POST or return default value
    public static function post($key, $default = null)
    {
        if (isset($_POST[$key])) {
            return htmlspecialchars(strip_tags($_POST[$key]));
        }
        return $default;
    }

    // Check if current HTTP request method is POST (else it is GET)
    public static function isPost()
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
}