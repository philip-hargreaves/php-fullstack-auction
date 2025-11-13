<?php
spl_autoload_register(
    function ($class) {


        // base directory for the root folder
        $base_dir = dirname(__DIR__) . '/';

        // "models\Item" becomes "models/Item.php"
        $file = $base_dir . str_replace('\\', '/', $class) . '.php';

        // if the file exists, require it
        if (file_exists($file)) {
            require $file;
        }
    });