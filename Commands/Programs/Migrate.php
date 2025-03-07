<?php

namespace Commands\Programs;

use Commands\AbstractCommand;
use Commands\Argument;

class Migrate extends AbstractCommand
{
    // 使用するコマンド名を設定
    protected static ?string $alias = 'migrate';

    // 引数を割り当て
    public static function getArguments(): array
    {
        return [
            (new Argument('rollback'))->description('Roll backwards. An integer n may also be provided to rollback n times.')->required(false)->allowAsShort(true),
        ];
    }

    public function execute(): int
    {
        $rollback = $this->getArgumentValue('rollback');
        if($rollback === false){
            $this->log("Starting migration......");
            $this->migrate();
        }
        else{
            // ロールバックは設定されている場合はtrue、またはそれに添付されている値が整数として表されます。
            $rollback = $rollback === true ? 1 : (int) $rollback;
            $this->log("Running rollback....");
            for($i = 0; $i < $rollback; $i++){
                $this->rollback();
            }
        }

        return 0;
    }
    private function migrate(): void {
        $this->log("Running migrations...");
        $this->log("Migration ended...\n");
    }

    private function rollback(): void {
        $this->log("Rolling back migration...\n");
    }
}