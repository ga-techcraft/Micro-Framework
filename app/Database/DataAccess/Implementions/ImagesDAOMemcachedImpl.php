<?php

namespace Database\DataAccess\Implementions;

use Database\Interfaces\ImagesDAO;
use Database\DatabaseManager;
use Models\Images;

class ImagesDAOMemcachedImpl implements ImagesDAO{
  public function create(Images $images): bool{
    $memcached = DatabaseManager::getMemcachedConnection();
    $memcached->set($images->getUniqueString(), $images->getImageName());
    return true;
  }
    
  public function getByUniqueString(string $uniqueString): ?Images{
    $memcached = DatabaseManager::getMemcachedConnection();
    $imageName = $memcached->get($uniqueString);
    return $imageName ? new Images($imageName, $uniqueString) : null;
  }
    
  public function update(Images $images): bool{
    $memcached = DatabaseManager::getMemcachedConnection();
    $memcached->set($images->getUniqueString(), $images->getImageName());
    return true;
  }
    
  public function delete(string $uniqueString): bool{
    $memcached = DatabaseManager::getMemcachedConnection();
    $memcached->delete($uniqueString);
    return true;
  }
    
  public function createOrUpdate(Images $images): bool{
    $memcached = DatabaseManager::getMemcachedConnection();
    $memcached->set($images->getUniqueString(), $images->getImageName());
    return true;
  }
}