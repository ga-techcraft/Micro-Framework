<?php

namespace Models\Traits;

trait GenericModel{
    // プロパティを連想配列に変換
    public function toArray(): array{
        return (array)$this;
    }
    
    // プロパティをJSON文字列に変換
    public function toString(): string{
        return json_encode($this, JSON_PRETTY_PRINT);
    }
}
