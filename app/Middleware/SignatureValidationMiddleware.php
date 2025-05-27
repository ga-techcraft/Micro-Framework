<?php

namespace Middleware;

use Response\FlashData;
use Response\HTTPRenderer;
use Response\Render\RedirectRenderer;
use Routing\Route;
use Helpers\ValidationHelper;

class SignatureValidationMiddleware implements Middleware
{
    public function handle(callable $next): HTTPRenderer
    {
        $currentPath = $_SERVER['REQUEST_URI'] ?? '';
        $parsedUrl = parse_url($currentPath);
        $pathWithoutQuery = $parsedUrl['path'] ?? '';

        // 現在のパスのRouteオブジェクトを作成します。
        $route = Route::create($pathWithoutQuery, function(){});

        // URLに有効な署名があるかチェックします。
        if ($route->isSignedURLValid($_SERVER['HTTP_HOST'] . $currentPath)) {
            // 署名が有効であれば、ミドルウェアチェインを進めます。
            if(isset($_GET['expiration']) && ValidationHelper::integer($_GET['expiration']) < time()){
              FlashData::setFlashData('error', "The URL has expired.");
              return new RedirectRenderer('');
          }
            return $next();
        } else {
            // 署名が有効でない場合、ランダムな部分にリダイレクトします。
            FlashData::setFlashData('error', sprintf("Invalid URL (%s).", $pathWithoutQuery));
            return new RedirectRenderer('');
        }
    }
}