<?php

class Router {
    private $routes = [];
    private $basePath = '';
    
    public function __construct($basePath = '') {
        $this->basePath = rtrim($basePath, '/');
    }
    
    /**
     * Agregar ruta GET
     */
    public function get($path, $callback) {
        $this->addRoute('GET', $path, $callback);
    }
    
    /**
     * Agregar ruta POST
     */
    public function post($path, $callback) {
        $this->addRoute('POST', $path, $callback);
    }
    
    /**
     * Agregar ruta que acepta GET y POST
     */
    public function any($path, $callback) {
        $this->addRoute(['GET', 'POST'], $path, $callback);
    }
    
    /**
     * Agregar ruta
     */
    private function addRoute($methods, $path, $callback) {
        if (!is_array($methods)) {
            $methods = [$methods];
        }
        
        $this->routes[] = [
            'methods' => $methods,
            'path' => $path,
            'callback' => $callback
        ];
    }
    
    /**
     * Resolver ruta actual
     */
    public function resolve() {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri = $_SERVER['REQUEST_URI'];
        
        // Remover query string
        if (($pos = strpos($requestUri, '?')) !== false) {
            $requestUri = substr($requestUri, 0, $pos);
        }
        
        // Remover base path
        if ($this->basePath && strpos($requestUri, $this->basePath) === 0) {
            $requestUri = substr($requestUri, strlen($this->basePath));
        }
        
        $requestUri = '/' . ltrim($requestUri, '/');
        
        foreach ($this->routes as $route) {
            if (in_array($requestMethod, $route['methods'])) {
                $pattern = $this->convertToRegex($route['path']);
                
                if (preg_match($pattern, $requestUri, $matches)) {
                    array_shift($matches); // Remover la coincidencia completa
                    return $this->callRoute($route['callback'], $matches);
                }
            }
        }
        
        // Ruta no encontrada
        $this->notFound();
    }
    
    /**
     * Convertir path a regex
     */
    private function convertToRegex($path) {
        // Escapar caracteres especiales excepto los parámetros
        $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $path);
        $pattern = '#^' . $pattern . '$#';
        return $pattern;
    }
    
    /**
     * Ejecutar callback de la ruta
     */
    private function callRoute($callback, $params = []) {
        if (is_string($callback)) {
            // Formato: 'Controller@method'
            $parts = explode('@', $callback);
            if (count($parts) === 2) {
                $controllerName = $parts[0];
                $methodName = $parts[1];
                
                $controllerFile = __DIR__ . "/../controllers/{$controllerName}.php";
                
                if (file_exists($controllerFile)) {
                    require_once $controllerFile;
                    
                    if (class_exists($controllerName)) {
                        $controller = new $controllerName();
                        
                        if (method_exists($controller, $methodName)) {
                            return call_user_func_array([$controller, $methodName], $params);
                        }
                    }
                }
            }
        } elseif (is_callable($callback)) {
            return call_user_func_array($callback, $params);
        }
        
        $this->notFound();
    }
    
    /**
     * Página no encontrada
     */
    private function notFound() {
        http_response_code(404);
        echo "<h1>404 - Página no encontrada</h1>";
        exit;
    }
}