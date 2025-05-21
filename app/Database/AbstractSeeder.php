<?php

namespace Database;

use Exception;

abstract class AbstractSeeder implements Seeder{
    protected ?string $tableName = null;
    protected array $tableData = [];

    const AVAILABLE_DATA_TYPES = [
        'int' => 'i',
        'string' => 's',
        'float' => 'd',
    ];

    public function seed(): void{
        $data = $this->createRowData();

        if ($this->tableName === null) throw new Exception("Table name is not set");
        if (empty($this->tableData)) throw new Exception("Table data is empty");

        foreach ($data as $row) {
            $this->validateRow($row);
            $this->insertRow($row);
        }
    }

    private function validateRow(array $row): void{
        if (count($row) !== count($this->tableData)) throw new Exception("Row data is not valid");

        foreach ($row as $index => $value) {
          $columnDataType = $this->tableData[$index]['data_type'];
          $columnName = $this->tableData[$index]['column_name'];

          if (!isset(self::AVAILABLE_DATA_TYPES[$columnDataType])) throw new Exception("Invalid data type: " . $columnDataType);

          if (get_debug_type($value) !== $columnDataType) throw new Exception("Invalid data type: " . $columnDataType);
        }
    }

    private function insertRow(array $row): void{
        $columnNames = array_map(function($item){
            return $item['column_name'];
        }, $this->tableData);

        $placeholders = str_repeat('?,', count($columnNames) - 1) . '?';

        $query = sprintf("INSERT INTO %s (%s  ) VALUES (%s);", $this->tableName, implode(',', $columnNames), $placeholders);

        DatabaseManager::getMysqliConnection()->prepareAndExecute($query, implode('', array_map(function($item){
            return self::AVAILABLE_DATA_TYPES[$item['data_type']];
        }, $this->tableData)), $row);
    }
}
