<?php
spl_autoload_extensions(".php");
spl_autoload_register(function($class) {
    $file = __DIR__ . '/../'  . str_replace('\\', '/', $class). '.php';
    if (file_exists(stream_resolve_include_path($file))) include($file);
});

// session_start();

$uri = $_SERVER['REQUEST_URI'];
$path = parse_url($uri, PHP_URL_PATH);
$path = trim($path, '/');

$routes = include __DIR__ . '/../Routing/routes.php';

if (isset($routes[$path])) {
  $route = $routes[$path];
  try {
    if (!($route instanceof \Routing\Route)) throw new \Exception('Route is not instance of Route');

    $middlewareRegister = include __DIR__ . '/../Middleware/middleware-register.php';
    $middlewares = array_merge($middlewareRegister['global'], array_map(fn($routeAlias) => $middlewareRegister['aliases'][$routeAlias], $route->getMiddleware()));
    $middlewareHandler = new \Middleware\MiddlewareHandler(array_map(fn($middlewareClass) => new $middlewareClass(), $middlewares));
  
    $renderer = $middlewareHandler->run($route->getCallback());
    foreach ($renderer->getField() as $key => $value) {
      header($key . ': ' . $value);
    }
    echo $renderer->getContent();
    exit;

  } catch (\Exception $e) {
    http_response_code(500);
    echo '500 Internal Server Error';
    exit;
  }

} else {
  http_response_code(404);
  echo '404 Not Found';
  exit;
}

http_response_code(500);
echo '500 Internal Server Error';
exit;
