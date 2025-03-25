<?php
namespace Database\Migrations;

use Database\SchemaMigration;

class Part implements SchemaMigration
{
    public function up(): array
    {
        // マイグレーションロジックをここに追加してください
        return [
            '
            CREATE TABLE IF NOT EXISTS parts (
                id INT PRIMARY KEY AUTO_INCREMENT,
                carID INT,
                name VARCHAR(255),
                description TEXT,
                price FLOAT,
                quantityInStock INT,
                FOREIGN KEY (carID) REFERENCES cars(id)
            );
            '
        ];
    }

    public function down(): array
    {
        // ロールバックロジックを追加してください
        return ['DROP TABLE parts'];
    }
}