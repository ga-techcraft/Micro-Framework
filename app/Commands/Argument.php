<?php

namespace Commands;

class Argument{
 private string $argument;
 private string $description;
 private bool $required = true;
 private bool $allowAsShort = false;
 private bool $isArgumentValueRequired = true;

 public function __construct(string $argument){
    $this->argument = $argument;
}

public function getArgument(): string{
    return $this->argument;
}

public function getDescription(): string{
    return $this->description;
}

public function description(string $description): Argument{
    $this->description = $description;
    return $this;
}

public function isRequired(): bool{
    return $this->required;
}

public function required(bool $required): Argument{
    $this->required = $required;
    return $this;
}

public function isShortAllowed(): bool{
    return $this->allowAsShort;
}

public function allowAsShort(bool $allowAsShort): Argument{
    $this->allowAsShort = $allowAsShort;
    return $this;
}

public function isArgumentValueRequired(): bool{
    return $this->isArgumentValueRequired;
}

public function argumentValueRequired(bool $isArgumentValueRequired): Argument{
    $this->isArgumentValueRequired = $isArgumentValueRequired;
    return $this;
}
}