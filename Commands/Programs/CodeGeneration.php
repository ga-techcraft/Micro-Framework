<?php

namespace Commands\Programs;

use Commands\AbstractCommand;

class CodeGeneration extends AbstractCommand
{
    // 使用するコマンド名を設定
    protected static ?string $alias = 'code-gen';
    protected static bool $requiredCommandValue = true;

    // 引数を割り当て
    public static function getArguments(): array
    {
        return [];
    }

    public function execute(): int
    {
        // 以下でコマンド名を取得する
        $codeGenType = $this->getCommandValue(); 

        // テンプレートファイルの取得
        $templete = include(dirname(__FILE__, 1) . "/Templete.php");

        // テンプレートにコマンド名を上書き
        $newCommandFile = "<?php \n" . sprintf($templete, $codeGenType);

        // 新しいコマンドのファイルの作成と初期データの書き込み
        file_put_contents(dirname(__FILE__, 1) . "/" . $codeGenType . ".php", $newCommandFile);

        $this->log('Generating code for.......' . $codeGenType);
        return 0;
    }
}