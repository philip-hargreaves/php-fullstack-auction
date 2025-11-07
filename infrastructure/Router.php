<?php

class Router {
    protected $routes = [
        'GET' => [],
        'POST' => [],
    ];

    public function get($uri, $controller)
    {
        // eg ['GET']['/listing'] = 'controller/listing.php'
        $this->routes['GET'][$uri] = $controller;
    }

    public function post($uri, $controller)
    {
        $this->routes['POST'][$uri] = $controller;
    }

    // Delete, Put, Patch to be added when needed

    public function route($uri, $method)
    {
        if ($this->routes[$method][$uri] ?? false) {
            return require base_path($this->routes[$method][$uri]);
            }
        $this->abort();
    }

    protected function abort($code = 404)
    {
        http_response_code($code);
        dd("{$code} Page Not Found");
    }
}



