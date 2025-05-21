<?php

namespace Commands\Programs;

use Commands\AbstractCommand;
use Commands\Argument;
use Exception;

class CodeGeneration extends AbstractCommand{
  public static bool $isRequiredCommandValue = true;
  protected static string $alias = 'code-gen';

  public static function getArguments(): array{
    return [
      (new Argument('name'))->required(true),
    ];
  }

  public function execute(): int{
    $codeNameToGenerate = $this->getArgumentValue(static::getAlias());
    $name = $this->getArgumentValue('name');

    if($codeNameToGenerate === 'migration'){
        $this->makeMigration($name);
    }
    else if($codeNameToGenerate === 'command'){
        $this->makeCommand($name);
    }
    else if($codeNameToGenerate === 'seeder'){
        $this->makeSeeder($name);
    } else {
      throw new Exception("Invalid code name to generate");
    }

    return 0;
  }

  private function makeMigration(string $name){
    // コンテンツ作成
    $content = <<< MIGRATION
    <?php

    namespace Database\Migration;

    use Database\SchemaMigration;

    class $name implements SchemaMigration{
        public function up(): array{
            return [];
        }

        public function down(): array{
            return [];
        }
    }
    MIGRATION;

    // パス作成
    $path = __DIR__ . '/../../Database/Migration/' .date('Y_m_d_His') . '_' . $name . '.php';

    // ファイル保存
    file_put_contents($path, $content);
  
  }

  private function makeCommand(string $name){
    // コンテンツ作成
    $content = <<< COMMAND
    <?php

    namespace Commands\Programs;

    use Commands\AbstractCommand;
    use Commands\Argument;

    class $name extends AbstractCommand{
        public static bool \$isRequiredCommandValue = true; // デフォルト値はfalse
        protected static string \$alias = '$name';

        public static function getArguments(): array{
            return [];
        }

        public function execute(): int{
            return 0;
        }
    }
    COMMAND;

    // パス作成
    $path = __DIR__ . "/$name.php";

    // ファイル保存
    file_put_contents($path, $content);
  }

  private function makeSeeder(string $name){
    // コンテンツ作成
    $content = <<< SEEDER
    <?php

    namespace Database\Seeds;

    use Database\AbstractSeeder;

    class $name extends AbstractSeeder{
        protected ?string \$tableName = null;
        protected array \$tableData = [
            [
                'data_type' => 'string',
                'column_name' => 'name',
            ],
            [
                'data_type' => 'int',
                'column_name' => 'age',
            ],
        ];

        public function createRowData(): array{
            return [
                [
                    'this is name string',
                    25,
                ],
            ];
        }
    }
    SEEDER;

    // パス作成
    $path = __DIR__ . '/../../Database/Seeds/'. $name . '.php';

    // ファイル保存
    file_put_contents($path, $content);
  }
}
