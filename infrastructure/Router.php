<?php
namespace infrastructure;
use infrastructure\Utilities;
class Router {
    protected $routes = [
        'GET' => [],
        'POST' => [],
    ];

    public function get($uri, $controller)
    {
        // eg ['GET']['/listing'] = 'controller/auction.php'
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
            return require_once Utilities::basePath($this->routes[$method][$uri]);
            }
        $this->abort();
    }

    protected function abort($code = 404)
    {
        http_response_code($code);
        Utilities::dd("{$code} Page Not Found");
    }
}



