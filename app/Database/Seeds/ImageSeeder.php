<?php

namespace Database\Seeds;

use Database\AbstractSeeder;
use Faker\Factory;

class ImageSeeder extends AbstractSeeder{
    protected ?string $tableName = 'images';
    protected array $tableData = [
        [
            'data_type' => 'string',
            'column_name' => 'image_name',
        ],
        [
            'data_type' => 'string',
            'column_name' => 'unique_string',
        ],
    ];

    public function createRowData(): array{
        $faker = Factory::create();
        $rows = [];

        for($i = 0; $i < 10; $i++){
            $rows[] = [
                $faker->word,
                $faker->uuid,
            ];
        }

        return $rows;
    }
}