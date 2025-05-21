<?php

namespace Helpers;

use Database\MySQLWrapper;

class DatabaseHelper{
    public static function insertImage(string $imageName, string $uniqueString): void{
        $mysqli = new MySQLWrapper();
        $query = "INSERT INTO images (image_name, unique_string) VALUES (?, ?)";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("ss", $imageName, $uniqueString);
        $stmt->execute();
        $stmt->close();
        $mysqli->close();
    }

    // 指定されたuniqueStringの画像名を返す。画像が存在しない場合は、nullを返す。
    public static function getImage(string $uniqueString): ?array{
        $mysqli = new MySQLWrapper();
        $query = "SELECT image_name FROM images WHERE unique_string = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("s", $uniqueString);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        $mysqli->close();
        return $result->num_rows === 0 ? null : $result->fetch_assoc();
    }

    public static function deleteImage(string $uniqueString): void{
        $mysqli = new MySQLWrapper();
        $query = "DELETE FROM images WHERE unique_string = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("s", $uniqueString);
        $stmt->execute();
        $stmt->close();
        $mysqli->close();
    }
}