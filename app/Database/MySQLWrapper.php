<?php

namespace Database;

use Helpers\Settings;
use mysqli;
use Exception;

class MySQLWrapper extends mysqli{

  public function __construct(){
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
      
    $host = Settings::readEnvInfo('DATABASE_HOST');
    $user = Settings::readEnvInfo('DATABASE_USER');
    $password = Settings::readEnvInfo('DATABASE_PASSWORD');
    $database = Settings::readEnvInfo('DATABASE_NAME');
    parent::__construct($host, $user, $password, $database);

  }

  public function prepareAndFetchAll(string $prepareQuery, string $types, array $data): ?array{
    $this->typesAndDataValidationPass($types, $data);

    $stmt = $this->prepare($prepareQuery);
    if(count($data) > 0) $stmt->bind_param($types, ...$data);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
  }

  public function prepareAndExecute(string $prepareQuery, string $types, array $data): bool{
    $this->typesAndDataValidationPass($types, $data);

    $stmt = $this->prepare($prepareQuery);
    if(count($data) > 0) $stmt->bind_param($types, ...$data);
    return $stmt->execute();
  }

  private function typesAndDataValidationPass(string $types, array $data): void{
    if (strlen($types) !== count($data)) throw new Exception(sprintf('Type and data must equal in length %s vs %s', strlen($types), count($data)));
  }
}
