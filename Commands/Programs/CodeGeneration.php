<?php

namespace Commands\Programs;

use Commands\AbstractCommand;
use Commands\Argument;

class CodeGeneration extends AbstractCommand
{
    // 使用するコマンド名を設定します
    protected static ?string $alias = 'code-gen';
    protected static bool $requiredCommandValue = true;

    // 引数を割り当てます
    public static function getArguments(): array
    {
        return [
            (new Argument('name'))->description('Name of the file that is to be generated.')->required(false),
        ];
    }

    public function execute(): int
    {
        $codeGenType = $this->getCommandValue();
        $this->log('Generating code for.......' . $codeGenType);

        if ($codeGenType === 'migration') {
            $migrationName = $this->getArgumentValue('name');
            $this->generateMigrationFile($migrationName);
        } else if ('command') {
            $commandName = $this->getArgumentValue('name');
            $this->generateCommandFile($commandName);
        }

        return 0;
    }

    private function generateMigrationFile(string $migrationName): void
    {
        $filename = sprintf(
            '%s_%s_%s.php',
            date('Y-m-d'),
            time(),
            $migrationName
        );

        $migrationContent = $this->getMigrationContent($migrationName);

        // 移行ファイルを保存するパスを指定します
        $path = sprintf("%s/../../Database/Migrations/%s", __DIR__,$filename);

        file_put_contents($path, $migrationContent);
        $this->log("Migration file {$filename} has been generated!");
    }

    private function getMigrationContent(string $migrationName): string
    {
        $className = $this->pascalCase($migrationName);

        return <<<MIGRATION
            <?php
            namespace Database\Migrations;

            use Database\SchemaMigration;

            class {$className} implements SchemaMigration
            {
                public function up(): array
                {
                    // マイグレーションロジックをここに追加してください
                    return [];
                }

                public function down(): array
                {
                    // ロールバックロジックを追加してください
                    return [];
                }
            }
            MIGRATION;
    }

    private function getCommandContent(string $commandName) :string
    {       
        $commandName = $this->pascalCase($commandName);

        return <<<COMMAND
            <?php
            namespace Commands\Programs;

            use Commands\AbstractCommand;
            use Commands\Argument;

            class $commandName extends AbstractCommand
            {
                // TODO: エイリアスを設定してください。
                protected static ?string \$alias = '{INSERT COMMAND HERE}';

                // TODO: 引数を設定してください。
                public static function getArguments(): array
                {
                    return [];
                }

                // TODO: 実行コードを記述してください。
                public function execute(): int
                {
                    return 0;
                }
            }
            COMMAND;
    }

    private function generateCommandFile(string $commandName): void
    {       
        $filename = $commandName;

        $commandContent = $this->getCommandContent($commandName);

        // 移行ファイルを保存するパスを指定します
        // $path = sprintf("%s/../../Database/Migrations/%s", __DIR__,$filename);
        $path = sprintf(dirname(__FILE__) . "/" . $commandName . ".php");

        file_put_contents($path, $commandContent);
        $this->log("Command file {$filename} has been generated!");
    }

    private function pascalCase(string $string): string{
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));
    }
}