<?php

namespace Models;

use Models\Interfaces\Model;
use Models\Traits\GenericModel;

class Images implements Model{
    use GenericModel;

    public function __construct(
      public string $image_name,
      public string $unique_string,
      public ?int $id = null
    ){}

    public function getId(): int{
        return $this->id;
    }

    public function getImageName(): string{
        return $this->image_name;
    }

    public function setImageName(string $image_name): void{
        $this->image_name = $image_name;
    }

    public function getUniqueString(): string{
        return $this->unique_string;
    }

    public function setUniqueString(string $unique_string): void{
        $this->unique_string = $unique_string;
    }
}