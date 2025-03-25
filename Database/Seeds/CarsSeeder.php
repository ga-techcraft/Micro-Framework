<?php
namespace Database\Seeds;

use Database\AbstractSeeder;
use Faker\Factory;

class CarsSeeder extends AbstractSeeder {

    // TODO: tableName文字列を割り当ててください。
    protected ?string $tableName = 'cars';

    // TODO: tableColumns配列を割り当ててください。
    protected array $tableColumns = [
        [
            'data_type' => 'string',
            'column_name' => 'make'
        ],
        [
            'data_type' => 'string',
            'column_name' => 'model'
        ],
        [
            'data_type' => 'int',
            'column_name' => 'year'
        ],
        [
            'data_type' => 'string',
            'column_name' => 'color'
        ],
        [
            'data_type' => 'float',
            'column_name' => 'price'
        ],
        [
            'data_type' => 'float',
            'column_name' => 'mileage'
        ],
        [
            'data_type' => 'string',
            'column_name' => 'transmission'
        ],
        [
            'data_type' => 'string',
            'column_name' => 'engine'
        ],
        [
            'data_type' => 'string',
            'column_name' => 'status'
        ],
    ];

    public function createRowData(): array
    {
        // TODO: createRowData()メソッドを実装してください。
        $faker = Factory::create();
        $rows = [];

        $transmissions = ['automatic', 'manual', 'semi-automatic'];
        $statuses = ['available', 'sold'];
    
        for ($i = 0; $i < 1000; $i++) {
            $rows[] = [
                $faker->company(),                        // make
                ucfirst($faker->word()),                 // model
                $faker->numberBetween(2000, 2025),       // year
                $faker->safeColorName(),                 // color
                $faker->randomFloat(2, 1000, 50000),     // price
                $faker->randomFloat(1, 0, 200000),        // mileage
                $faker->randomElement($transmissions),   // transmission
                $faker->bothify('###HP V?'),             // engine 例: "320HP V6"
                $faker->randomElement($statuses)         // status
            ];
        }
        return $rows;
    }
}