<?php
header("Access-Control-Allow-Origin: *"); // 開発用、何でも許可

spl_autoload_extensions(".php");
spl_autoload_register(function($class) {
    $file = __DIR__ . '/'  . str_replace('\\', '/', $class). '.php';
    if (file_exists(stream_resolve_include_path($file))) include($file);
});


$DEBUG = true;

if (preg_match('/\.(?:png|jpg|jpeg|gif|js|css|html)$/', $_SERVER["REQUEST_URI"])) {
  return false;
}

// ルートを読み込みます。
$routes = include('Routing/routes.php');

// リクエストURIを解析してパスだけを取得します。
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = ltrim($path, '/');

// ルートにパスが存在するかチェックする
if (isset($routes[$path])) {
    // コールバックを呼び出してrendererを作成します。
    $renderer = $routes[$path]();

    try{
        // ヘッダーを設定します。
        foreach ($renderer->getFields() as $name => $value) {
          // 改行コードがあるか確認 → セキュリティ対策（ヘッダーインジェクション防止）
          if (preg_match('/[\r\n]/', $value)) {
              http_response_code(500);
              if ($DEBUG) print("Invalid header value: '$value'");
              exit;
          }
      
          header("$name: $value");
          print($renderer->getContent());
        }
    }
    catch (Exception $e){
        http_response_code(500);
        print("Internal error, please contact the admin.<br>");
        if($DEBUG) print($e->getMessage());
    }
} else {
    // マッチするルートがない場合、404エラーを表示します。
    http_response_code(404);
    echo "404 Not Found: The requested route was not found on this server.";
}