<?php
spl_autoload_extensions(".php");
spl_autoload_register(function($class) {
    $file = __DIR__ . '/../'  . str_replace('\\', '/', $class). '.php';
    if (file_exists(stream_resolve_include_path($file))) include($file);
});

// リクエストのURLを取得
$uri = $_SERVER['REQUEST_URI'];
$path = parse_url($uri, PHP_URL_PATH);
$path = trim($path, '/');

// ルートを取得
$routes = include __DIR__ . '/../Routing/routes.php';

// ルートが存在するか確認
if (isset($routes[$path])) {
  $route = $routes[$path];
  try {
    if (!($route instanceof \Routing\Route)) throw new \Exception('Route is not instance of Route');

    // ミドルウェアの登録
    $middlewareRegister = include __DIR__ . '/../Middleware/middleware-register.php';
    $middlewares = array_merge($middlewareRegister['global'], array_map(fn($routeAlias) => $middlewareRegister['aliases'][$routeAlias], $route->getMiddleware()));
    $middlewareHandler = new \Middleware\MiddlewareHandler(array_map(fn($middlewareClass) => new $middlewareClass(), $middlewares));
  
    // ミドルウェアを実行
    $renderer = $middlewareHandler->run($route->getCallback());
    
    // HTTPヘッダーを設定
    foreach ($renderer->getField() as $key => $value) {
      header($key . ': ' . $value);
    }
    
    // レンダリング結果を出力
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
