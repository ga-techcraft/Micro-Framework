<?php

namespace Database\Seeds;

use Database\AbstractSeeder;
use Faker\Factory;

class ComputerPartsSeeder extends AbstractSeeder {
    protected ?string $tableName = 'computer_parts';
    protected array $tableColumns = [
        [
            'data_type' => 'string',
            'column_name' => 'name'
        ],
        [
            'data_type' => 'string',
            'column_name' => 'type'
        ],
        [
            'data_type' => 'string',
            'column_name' => 'brand'
        ],
        [
            'data_type' => 'string',
            'column_name' => 'model_number'
        ],
        [
            'data_type' => 'string',
            'column_name' => 'release_date'
        ],
        [
            'data_type' => 'string',
            'column_name' => 'description'
        ],
        [
            'data_type' => 'int',
            'column_name' => 'performance_score'
        ],
        [
            'data_type' => 'float',
            'column_name' => 'market_price'
        ],
        [
            'data_type' => 'float',
            'column_name' => 'rsm'
        ],
        [
            'data_type' => 'float',
            'column_name' => 'power_consumptionw'
        ],
        [
            'data_type' => 'float',
            'column_name' => 'lengthm'
        ],
        [
            'data_type' => 'float',
            'column_name' => 'widthm'
        ],
        [
            'data_type' => 'float',
            'column_name' => 'heightm'
        ],
        [
            'data_type' => 'int',
            'column_name' => 'lifespan'
        ]
    ];

    public function createRowData(): array {
      $faker = Factory::create();
      $rows = [];

      for ($i = 0; $i < 1000; $i++) {
        $rows[] = [
          $faker->name(), // name
          $faker->randomElement(['CPU', 'GPU', 'SSD', 'RAM']), // type
          $faker->company(), // brand
          strtoupper($faker->bothify('???-####')), // model_number
          $faker->date(), // release_data
          $faker->sentence(), // description
          $faker->numberBetween(50, 100), // performance_score
          $faker->randomFloat(2, 50, 1000), // market_price
          $faker->randomFloat(2, 0.01, 0.1), // rsm
          $faker->randomFloat(2, 50, 500), // power_consumptionw
          $faker->randomFloat(3, 0.01, 0.5), // lengthm
          $faker->randomFloat(3, 0.01, 0.5), // widthm
          $faker->randomFloat(4, 0.001, 0.1), // heightm
          $faker->numberBetween(1, 10) // lifespan
        ];
      }

      return $rows;

    }
}