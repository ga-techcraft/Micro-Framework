<?php

namespace Response;

interface HTTPRenderer{
    public function getField(): array;
    public function getContent(): string;
}
    