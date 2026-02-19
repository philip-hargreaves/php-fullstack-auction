<?php
spl_autoload_register(
    function ($class) {
        $base_dir = dirname(__DIR__) . '/';
        $file = $base_dir . str_replace('\\', '/', $class) . '.php';

        if (file_exists($file)) {
            require $file;
        }
    });