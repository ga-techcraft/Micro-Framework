<?php

namespace Database\DataAccess\Implementions;

use Database\Interfaces\ImagesDAO;
use Database\DatabaseManager;
use Models\Images;

class ImagesDAOMySQLImpl implements ImagesDAO{
  public function create(Images $images): bool{
    $mysqli = DatabaseManager::getMysqliConnection();
    $query = "INSERT INTO images (image_name, unique_string) VALUES (?, ?)";
    return $mysqli->prepareAndExecute($query, 'ss', [$images->getImageName(), $images->getUniqueString()]);
  }

  public function getByUniqueString(string $uniqueString): ?Images{
    $mysqli = DatabaseManager::getMysqliConnection();
    $query = "SELECT * FROM images WHERE unique_string = ?";
    $result = $mysqli->prepareAndFetchAll($query, 's', [$uniqueString]);
    return $result ? $this->resultToImage($result[0]) : null;
  }

  public function update(Images $images): bool{
    $mysqli = DatabaseManager::getMysqliConnection();
    $query = "UPDATE images SET image_name = ?, unique_string = ? WHERE id = ?";
    return $mysqli->prepareAndExecute($query, 'ssi', [$images->getImageName(), $images->getUniqueString(), $images->getId()]);
  }

  public function delete(string $uniqueString): bool{
    $mysqli = DatabaseManager::getMysqliConnection();
    $query = "DELETE FROM images WHERE unique_string = ?";
    return $mysqli->prepareAndExecute($query, 's', [$uniqueString]);
  }

  public function createOrUpdate(Images $images): bool{
    $mysqli = DatabaseManager::getMysqliConnection();
    $query = "INSERT INTO images (image_name, unique_string) VALUES (?, ?) ON DUPLICATE KEY UPDATE image_name = VALUES(image_name), unique_string = VALUES(unique_string)";
    return $mysqli->prepareAndExecute($query, 'ss', [$images->getImageName(), $images->getUniqueString()]);
  }

  private function resultToImage(array $result): Images{
    return new Images($result['image_name'], $result['unique_string'], $result['id']);
  }

  private function resultsToImages(array $results): array{
    return array_map(function($result){
      return $this->resultToImage($result);
    }, $results);
  }
}