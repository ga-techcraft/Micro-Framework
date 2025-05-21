<?php
spl_autoload_extensions(".php");
spl_autoload_register(function($class) {
    $file = __DIR__ . '/../'  . str_replace('\\', '/', $class). '.php';
    if (file_exists(stream_resolve_include_path($file))) include($file);
});

$uri = $_SERVER['REQUEST_URI'];
$path = parse_url($uri, PHP_URL_PATH);
$path = trim($path, '/');

$routes = include __DIR__ . '/../Routing/routes.php';

if (isset($routes[$path])) {

  $renderer = $routes[$path]();
  foreach ($renderer->getField() as $key => $value) {
    header($key . ': ' . $value);
  }
  echo $renderer->getContent();
  exit;

} else {
  http_response_code(404);
  echo '404 Not Found';
  exit;
}

http_response_code(500);
echo '500 Internal Server Error';
exit;
