<?php

namespace Models\Interfaces;

interface Model{
    public function toArray(): array;
    public function toString(): string;
}