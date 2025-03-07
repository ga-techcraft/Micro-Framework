<?php

use Database\MySQLWrapper;

$mysqli = new MySQLWrapper();

// carsテーブルの作成
$result = $mysqli->query(file_get_contents(__DIR__ . '/cars-setup.sql'));
if($result === false) throw new Exception('Could not execute query.');
else print("Successfully ran all SQL setup queries.".PHP_EOL);

// partsテーブルの作成
$result = $mysqli->query(file_get_contents(__DIR__ . '/parts-setup.sql'));
if($result === false) throw new Exception('Could not execute query.');
else print("Successfully ran all SQL setup queries.".PHP_EOL);

// carsテーブルにレコード追加
runQuery($mysqli, insertCarQuery(
  make: 'Toyota',
  model: 'Corolla',
  year: 2020,
  color: 'Blue',
  price: 20000,
  mileage: 1500,
  transmission: 'Automatic',
  engine: 'Gasoline',
  status: 'Available'
));

// partsテーブルにレコード追加
runQuery($mysqli, insertPartQuery(
  name: 'Brake Pad',
  description: 'High Quality Brake Pad',
  price: 45.99,
  quantityInStock: 100
));

$mysqli->close();


// ---------------以下は定義-----------------
// partsテーブルにレコードを追加する
function insertCarQuery(
  string $make,
  string $model,
  int $year,
  string $color,
  float $price,
  float $mileage,
  string $transmission,
  string $engine,
  string $status
): string {
  return sprintf(
    "INSERT INTO cars (make, model, year, color, price, mileage, transmission, engine, status)
    VALUES ('%s', '%s', %d, '%s', %f, %f, '%s', '%s', '%s');",
    $make, $model, $year, $color, $price, $mileage, $transmission, $engine, $status
  );
}

// partssテーブルにレコードを追加する
function insertPartQuery(
  string $name,
  string $description,
  float $price,
  int $quantityInStock
): string {
  return sprintf(
    "INSERT INTO parts (name, description, price, quantityInStock)
    VALUES ('%s', '%s', %f, %d);",
    $name, $description, $price, $quantityInStock
  );
}

// クエリの実行
function runQuery(mysqli $mysqli, string $query): void {
  $result = $mysqli->query($query);
  if ($result === false) {
    throw new Exception('Could not execute query.');
  } else {
    echo "Query executed successfully.\n";
  }
}