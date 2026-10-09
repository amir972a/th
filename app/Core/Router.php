<?php
class Router {
    private $routes = [];

    public function get($route, $action) {
        $this->addRoute('GET', $route, $action);
    }

    public function post($route, $action) {
        $this->addRoute('POST', $route, $action);
    }

    private function addRoute($method, $route, $action) {
        // Convert route variables {id} to regex
        $routeRegex = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<\1>[a-zA-Z0-9_-]+)', $route);
        $routeRegex = '#^' . $routeRegex . '$#';

        $this->routes[] = [
            'method' => $method,
            'route' => $routeRegex,
            'action' => $action
        ];
    }

    public function dispatch($uri, $method) {
        // Remove query string and base path if any
        $uri = parse_url($uri, PHP_URL_PATH);

        // Strip out the base URL path if the app is not in the root
        $basePath = parse_url(BASE_URL, PHP_URL_PATH);
        if (strpos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath));
        }
        if (empty($uri)) {
            $uri = '/';
        }

        foreach ($this->routes as $route) {
            if ($route['method'] == $method && preg_match($route['route'], $uri, $matches)) {
                $action = explode('@', $route['action']);
                $controllerName = $action[0];
                $methodName = $action[1];

                $params = [];
                // Only take numeric keys to avoid PHP 8 named argument mismatch errors
                foreach ($matches as $key => $value) {
                    if (is_int($key) && $key > 0) { // Skip full match ($key 0)
                        $params[] = $value;
                    }
                }

                require_once __DIR__ . '/../Controllers/' . $controllerName . '.php';
                $controller = new $controllerName();
                call_user_func_array([$controller, $methodName], $params);
                return;
            }
        }

        // 404 Not Found
        http_response_code(404);
        echo "404 Not Found";
    }
}
