<?php

namespace Response\Render;

use Response\HTTPRenderer;

class BinaryRenderer implements HTTPRenderer{  
    private string $mimeType;
    private string $uniqueString;

    public function __construct(string $mimeType, string $uniqueString){
        $this->mimeType = $mimeType;
        $this->uniqueString = $uniqueString;
    }

    public function getField(): array{  
        return [
            'Content-Type' => $this->mimeType,
        ];
    }

    public function getContent(): string{
        $filePath = __DIR__ . '/../../storage/images/' . $this->uniqueString;
        return file_get_contents($filePath);
    }
    
}