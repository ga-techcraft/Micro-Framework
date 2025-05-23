<?php

namespace Response\Render;

use Response\HTTPRenderer;
use Exception;
use Helpers\Authenticate;

class HTMLRenderer implements HTTPRenderer{
    private string $view;
    private array $data;

    public function __construct(string $view, array $data = []){
        $this->view = $view;
        $this->data = $data;
    }

    public function getField(): array{
        return [
            'Content-Type' => 'text/html',
        ];
    }

    public function getContent(): string{
        // $this->viewからviewのパスを取得する
        $viewPath = $this->getViewPath($this->view);

        if(!file_exists($viewPath)){
            throw new Exception("View not found: " . $viewPath);
        }

        extract($this->data);

        ob_start();
        include $viewPath;
        $content = ob_get_clean();

        return $this->getHeader() . $content . $this->getFooter();
    }

    public function getViewPath($view): string{
        return __DIR__ . '/../../Views/' . $view . '.php';
    }

    public function getHeader(): string{
        ob_start();
        $user = Authenticate::getAuthenticatedUser();
        include $this->getViewPath('layout/header');
        include $this->getViewPath('component/navigator');
        include $this->getViewPath('component/message-boxes');
        return ob_get_clean();
    }

    public function getFooter(): string{
        ob_start();
        include __DIR__ . '/../../Views/layout/footer.php';
        return ob_get_clean();
    }

}