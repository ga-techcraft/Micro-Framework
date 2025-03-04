<?php

use Database\MySQLWrapper;

$mysqli = new MySQLWrapper();

$result = $mysqli->query("
    CREATE TABLE IF NOT EXISTS cars (
      id INT PRIMARY KEY AUTO_INCREMENT,
      make VARCHAR(50),
      model VARCHAR(50),
      year INT,
      color VARCHAR(20),
      price FLOAT,
      mileage FLOAT,
      transmission VARCHAR(20),
      engine VARCHAR(20),
      status VARCHAR(10)
    );
");

if($result === false) throw new Exception('Could not execute query.');
else print("Successfully ran all SQL setup queries.".PHP_EOL);