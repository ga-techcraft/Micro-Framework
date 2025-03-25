<?php

namespace Database\Seeds;

use Database\AbstractSeeder;
use Faker\Factory;

class PartsSeeder extends AbstractSeeder
{

    // TODO: tableName文字列を割り当ててください。
    protected ?string $tableName = 'parts';

    // TODO: tableColumns配列を割り当ててください。
    protected array $tableColumns = [
        [
            'data_type' => 'int',
            'column_name' => 'carID'
        ],
        [
            'data_type' => 'string',
            'column_name' => 'name'
        ],
        [
            'data_type' => 'string',
            'column_name' => 'description'
        ],
        [
            'data_type' => 'float',
            'column_name' => 'price'
        ],
        [
            'data_type' => 'int',
            'column_name' => 'quantityInStock'
        ],
    ];

    public function createRowData(): array
    {
        // TODO: createRowData()メソッドを実装してください。
        $faker = Factory::create();
        $rows = [];

        for ($i = 0; $i < 100000; $i++) {
            $rows[] = [
                $faker->numberBetween(1, 1000), // carID
                $faker->words(2, true), // name
                $faker->sentence(10), // description
                $faker->randomFloat(2, 10, 1000), // price
                $faker->numberBetween(0, 500) // quantityInStock
            ];
        }
        return $rows;
    }
}