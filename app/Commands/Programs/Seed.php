<?php

namespace Commands\Programs;

use Commands\AbstractCommand;
use Commands\Argument;

class Seed extends AbstractCommand{
    public static bool $isRequiredCommandValue = false;
    protected static string $alias = 'seed';

    public static function getArguments(): array{
        return [];
    }

    public function execute(): int{
        $this->runAllSeed();
        return 0;
    }

    private function runAllSeed(){
        $allFiles = scandir(__DIR__ . '/../../Database/Seeds/');

        foreach ($allFiles as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                $className = 'Database\Seeds\\' . pathinfo($file, PATHINFO_FILENAME);
                $seeder = new $className();
                $seeder->seed();
            }
        }
    }
}