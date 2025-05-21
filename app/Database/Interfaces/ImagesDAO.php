<?php

namespace Database\Interfaces;

use Models\Images;

interface ImagesDAO{
    public function create(Images $images): bool;
    public function update(Images $images): bool;
    public function delete(string $uniqueString): bool;
    public function createOrUpdate(Images $images): bool;
    public function getByUniqueString(string $uniqueString): ?Images;
}