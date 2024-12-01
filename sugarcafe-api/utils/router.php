<?php
class Router {
    private $routes = [];

    public function addRoute($method, $path, $handler) {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler
        ];
    }

    public function get($path, $handler) {
        $this->addRoute('GET', $path, $handler);
    }

    public function post($path, $handler) {
        $this->addRoute('POST', $path, $handler);
    }

    public function put($path, $handler) {
        $this->addRoute('PUT', $path, $handler);
    }

    public function delete($path, $handler) {
        $this->addRoute('DELETE', $path, $handler);
    }

    public function dispatch() {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        foreach ($this->routes as $route) {
            $pattern = $this->convertRouteToRegex($route['path']);
            if ($route['method'] === $method && preg_match($pattern, $path, $matches)) {
                array_shift($matches); // Remove the full match
                $handler = $route['handler'];
                if (is_array($handler)) {
                    $controller = new $handler[0]();
                    $action = $handler[1];
                    $controller->$action(...$matches);
                } else {
                    $handler(...$matches);
                }
                return;
            }
        }

        // If no route matches, return a 404 response
        header("HTTP/1.0 404 Not Found");
        echo json_encode(['error' => 'Not Found']);
    }

    private function convertRouteToRegex($route) {
        return '#^' . preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^/]+)', $route) . '$#';
    }
}

