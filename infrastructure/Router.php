<?php
namespace infrastructure;

class Router
{
    protected array $routes = [
        'GET' => [],
        'POST' => [],
        'PUT' => [],
        'DELETE' => [],
    ];

    public function get(string $uri, string $action): void
    {
        $this->routes['GET'][$uri] = $action;
    }

    public function post(string $uri, string $action): void
    {
        $this->routes['POST'][$uri] = $action;
    }

    public function put(string $uri, string $action): void
    {
        $this->routes['PUT'][$uri] = $action;
    }

    public function delete(string $uri, string $action): void
    {
        $this->routes['DELETE'][$uri] = $action;
    }

    public function route(string $uri, string $method): mixed
    {
        // Method override for HTML forms (PUT/DELETE via _method)
        if ($method === 'POST' && isset($_POST['_method'])) {
            $method = strtoupper($_POST['_method']);
        }

        foreach ($this->routes[$method] as $pattern => $action) {
            $params = $this->match($pattern, $uri);
            if ($params !== false) {
                return $this->dispatch($action, $params);
            }
        }

        $this->abort(404);
        return null;
    }

    protected function match(string $pattern, string $uri): array|false
    {
        // Exact match (no params)
        if ($pattern === $uri) {
            return [];
        }

        // Pattern with {param} placeholders
        if (strpos($pattern, '{') === false) {
            return false;
        }

        $regex = preg_replace('/\{([a-zA-Z_]+)\}/', '([^/]+)', $pattern);
        $regex = '#^' . $regex . '$#';

        if (preg_match($regex, $uri, $matches)) {
            array_shift($matches);
            preg_match_all('/\{([a-zA-Z_]+)\}/', $pattern, $paramNames);
            return array_combine($paramNames[1], $matches);
        }

        return false;
    }

    protected function dispatch(string $action, array $params): mixed
    {
        // Support old-style file includes (for gradual migration)
        if (str_ends_with($action, '.php')) {
            return require_once Utilities::basePath($action);
        }

        // New style: Controller@method
        [$controllerClass, $method] = explode('@', $action);
        $fullClass = "app\\http\\controllers\\{$controllerClass}";

        if (!class_exists($fullClass)) {
            $this->abort(500);
        }

        $controller = new $fullClass();

        if (!method_exists($controller, $method)) {
            $this->abort(500);
        }

        return $controller->$method($params);
    }

    protected function abort(int $code = 404): void
    {
        http_response_code($code);
        echo "{$code} - Page Not Found";
        exit;
    }
}



