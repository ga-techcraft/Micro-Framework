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
        } else if ($codeGenType === 'command') {
            $commandName = $this->getArgumentValue('name');
            $this->generateCommandFile($commandName);
        } else if ($codeGenType === 'seed') {
            $seedName = $this->getArgumentValue('name');
            $this->generateSeedFile($seedName);
        } else {
            $this->log("The given type does not exist.");
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

    private function generateCommandFile(string $commandName): void
    {       
        // コマンド名がそのままファイル名になる
        $filename = $commandName;

        // パスカルケースに変換する
        $commandClassName = $this->pascalCase($commandName);

        $commandContent = $this->getCommandContent($commandClassName);

        // 移行ファイルを保存するパスを指定します
        $path = sprintf(dirname(__FILE__) . "/" . $commandName . ".php");

        file_put_contents($path, $commandContent);

        // registry.phpに追加
        $this->addRegistry($commandClassName);

        $this->log("Command file {$filename} has been generated!");
    }

    private function getCommandContent(string $commandClassName) :string
    {       
        return <<<COMMAND
            <?php
            namespace Commands\Programs;

            use Commands\AbstractCommand;
            use Commands\Argument;

            class $commandClassName extends AbstractCommand
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

    private function addRegistry(string $commandClassName): void
    {
        // registry.phpからコマンドリストを取得する
        $registryFilePath = dirname(__DIR__) . '/registry.php';
        $commandsList = require($registryFilePath);

        // 新しいコマンドを追加する
        $commandsList[] = "Commands\\Programs\\$commandClassName";

        $newRegistry = var_export($commandsList, true);

        // registry.phpに再びphpコードとして書き写す
        $registryFileContent = <<<PHP
        <?php

        return $newRegistry;

        PHP;

        file_put_contents($registryFilePath, $registryFileContent);
    }

    private function generateSeedFile(string $seedName): void{
        $filename = $seedName . ".php";

        $seedContent = $this->getSeedContent($seedName);

        // 移行ファイルを保存するパスを指定します
        $path = sprintf("%s/../../Database/Seeds/%s", __DIR__,$filename);

        file_put_contents($path, $seedContent);
        $this->log("Migration file {$filename} has been generated!");
    }

    private function getSeedContent($seedName): string{
        return <<<SEED
        <?php
        namespace Database\Seeds;

        use Database\AbstractSeeder;

        class $seedName extends AbstractSeeder {

            // TODO: tableName文字列を割り当ててください。
            protected ?string \$tableName = null;

            // TODO: tableColumns配列を割り当ててください。
            protected array \$tableColumns = [];

            public function createRowData(): array
            {
                // TODO: createRowData()メソッドを実装してください。
                return [];
            }
        }
        SEED;
    }

    private function pascalCase(string $string): string{
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));
    }
}