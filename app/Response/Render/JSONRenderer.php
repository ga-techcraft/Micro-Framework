<?php

namespace Response\Render;

use Response\HTTPRenderer;

class JSONRenderer implements HTTPRenderer{
    private array $data;

    public function __construct(array $data){
        $this->data = $data;
    }

    public function getField(): array{
        return [
            'Content-Type' => 'application/json',
        ];
    }

    public function getContent(): string{
        return json_encode($this->data);
    }
}