<?php

namespace Commands\Programs;

use Commands\AbstractCommand;
use Commands\Argument;
use Database\MySQLWrapper;
use Helpers\Settings;

class DBWipe extends AbstractCommand
{
    // 使用するコマンド名を設定
    protected static ?string $alias = 'db-wipe';

    // 引数を割り当て
    public static function getArguments(): array
    {
        return [new Argument('backup')->description('backup the data')->required(false)->allowAsShort(true)];
    }

    public function execute(): int
    {
        // databaseは.envにあるデータベースが指定されるようにする
        $database = Settings::env("DATABASE_NAME");

        $backup = $this->getArgumentValue('backup');
        
        if ($backup) {
          $this->backup($database);
        }
        
        $this->wipe($database);
        $this->log('Wiping the database.......');
        return 0;
    }

    private function wipe($database): void 
    {
      $mysqli = new MySQLWrapper();

      $result = $mysqli->query("DROP DATABASE $database;");

      if ($result === false) {
        throw new \Exception("Could not execute query for wiping the $database.");
      } else {
        $this->log("Successfully ran SQL for wiping the $database".PHP_EOL);
      }

      $mysqli->close();
    }

    private function backup($database): void
    {
      $username = $username??Settings::env('DATABASE_USER');
      $database = $database??Settings::env('DATABASE_NAME');
      $backupFile = dirname(__DIR__, 2) . '/backup.sql';

      $command = "mysqldump -u $username -p $database > $backupFile";

      exec($command, $output, $return_status);

      if ($return_status === 0) {
        $this->log("Backup successful! The dump is saved in $backupFile.");
      } else {
        $this->log("Error occurred during the backup process.");
      }
    }
}