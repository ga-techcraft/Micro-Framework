<?php

namespace Helpers;

use Database\MySQLWrapper;
use Exception;

class DatabaseHelper
{
    public static function getRandomComputerPart(): array{
        $db = new MySQLWrapper();

        $stmt = $db->prepare("SELECT * FROM computer_parts ORDER BY RAND() LIMIT 1");
        $stmt->execute();
        $result = $stmt->get_result();
        $part = $result->fetch_assoc();

        if (!$part) throw new Exception('Could not find a single part in database');

        return $part;
    }

    public static function getComputerPartById(int $id): array{
        $db = new MySQLWrapper();

        $stmt = $db->prepare("SELECT * FROM computer_parts WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();

        $result = $stmt->get_result();
        $part = $result->fetch_assoc();

        if (!$part) throw new Exception('Could not find a single part in database');

        return $part;
    }

    public static function getComputerPartByType(string $type, int $limit, int $offset): array{
      $db = new MySQLWrapper();
      
      $stmt = $db->prepare("SELECT * FROM computer_parts WHERE type = ? LIMIT ? OFFSET ?");
      $stmt->bind_param('sii', $type, $limit, $offset);
      $stmt->execute();

      $result = $stmt->get_result();
      $parts = $result->fetch_all(MYSQLI_ASSOC);

      if (!$parts) throw new Exception('Could not find a single part in database');

      return $parts;
    }

    public static function getCountComputerPartByType(string $type): int{
      $db = new MySQLWrapper();

      $stmt = $db->prepare("SELECT COUNT(*) as count FROM computer_parts WHERE type = ?");
      $stmt->bind_param('s', $type);
      $stmt->execute();

      $result = $stmt->get_result();
      $count = $result->fetch_assoc()['count'];

      if (!$count) throw new Exception('Could not find the computer part count in database');

      return $count;
      return 0;
    }

    public static function getCountComputerPart(): int{
      $db = new MySQLWrapper();

      $stmt = $db->prepare("SELECT COUNT(*) as count FROM computer_parts");
      $stmt->execute();

      $result = $stmt->get_result();
      $count = $result->fetch_assoc()['count'];

      return $count;
    }

    public static function getNewestComputerPart(): array{
      $db = new MySQLWrapper();

      $result = $db->query("SELECT * FROM computer_parts ORDER BY created_at DESC LIMIT 1");
      
      return $result->fetch_assoc();
    }

    public static function getTopPerformanceComputerPart(): array{
      $db = new MySQLWrapper();

      $result = $db->query("SELECT * FROM computer_parts ORDER BY performance_score DESC LIMIT 50");
      
      return $result->fetch_all(MYSQLI_ASSOC);
    }
}