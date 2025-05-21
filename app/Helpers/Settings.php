<?php

namespace Helpers;

class Settings{
  public static function readEnvInfo($key){
    $envInfo = parse_ini_file(__DIR__ . '/../.env');

    if (!isset($envInfo[$key])) {
      throw new Exception("Environment variable $key not found");
    }

    return $envInfo[$key];
  }
}
