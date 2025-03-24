<?php
namespace Commands\Programs;

use Commands\AbstractCommand;
use Commands\Argument;
use Helpers\Settings;

class DBRestore extends AbstractCommand
{
    // TODO: エイリアスを設定してください。
    protected static ?string $alias = 'db-restore';

    // TODO: 引数を設定してください。
    public static function getArguments(): array
    {
        return [];
    }

    // TODO: 実行コードを記述してください。
    public function execute(): int
    {
        $backupFilePath = dirname(__DIR__, 2) . '/backup.sql';
        $backupFile = file_get_contents($backupFilePath);
        
        // backup.sqlファイルが存在しない場合
        if ($backupFile == false) {
            throw new \Exception("The backup.sql file was not found.");
        }
        
        // データベース接続情報を取得
        $database_name = Settings::env('DATABASE_NAME');
        $user_name = Settings::env('DATABASE_USER');

        // DB作成コマンド
        $createDBCommand = sprintf("mysql -u %s -p -e \"CREATE DATABASE IF NOT EXISTS %s\"", $user_name, $database_name);
        exec($createDBCommand, $_, $result_code);

        // データベース接続に失敗した場合
        if ($result_code != 0) {
            throw new \Exception("Backup failed.");
        }

        // DB復元コマンド
        $backupDBCommand = sprintf("mysql -u %s -p %s < %s", $user_name, $database_name, $backupFilePath);
        exec($backupDBCommand, $_, $result_code);

        // データベース接続に失敗した場合
        if ($result_code != 0) {
            throw new \Exception("Backup failed.");
        }

        $this->log("Database restoration completed successfully.");

        return 0;
    }
}