<?php

namespace Database\Migration;

use Database\SchemaMigration;

class images implements SchemaMigration{
    public function up(): array{
        return [
            "CREATE TABLE images (
                id INT AUTO_INCREMENT PRIMARY KEY,
                image_name VARCHAR(255) NOT NULL,
                unique_string VARCHAR(255) NOT NULL UNIQUE,
                view_count INT DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
            )",
        ];
    }

    public function down(): array{
        return [
            "DROP TABLE images",
        ];
    }
}