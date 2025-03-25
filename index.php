<?php

spl_autoload_extensions(".php");
spl_autoload_register(function($class) {
    $file = __DIR__ . '/'  . str_replace('\\', '/', $class). '.php';
    if (file_exists(stream_resolve_include_path($file))) include($file);
});

// ルートをロードします。
$routes = include('Routing/routes.php');

// リクエストURIを解析してパスだけを取得します。
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = ltrim($path, '/');

// ルートにパスが存在するかチェックします。
if (isset($routes[$path])) {
    $view = $routes[$path];
    $viewPath = sprintf("%s/Views/%s.php",__DIR__, $view);

    if (file_exists($viewPath)) {
        // ヘッダーを設定します。
        include 'Views/layout/header.php';
        include $viewPath;
        include 'Views/layout/footer.php';
    } else {
        http_response_code(500);
        printf("<br>debug info:<br>%s<br>%s", "Internal error, please contact the admin.");
    }
} else {
    // 一致するルートがない場合、404エラーを表示する
    http_response_code(404);
    echo "404 Not Found: The requested route was not found on this server.";
    printf("<br>debug info:<br>%s<br>%s", json_encode($routes),$path);
}