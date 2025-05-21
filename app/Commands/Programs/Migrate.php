<?php

namespace Commands\Programs;

use Commands\AbstractCommand;
use Commands\Argument;
use Database\MySQLWrapper;
use Exception;

class Migrate extends AbstractCommand{
    protected static string $alias = 'migrate';
    public static bool $isRequiredCommandValue = false;

    public static function getArguments(): array{
        return [
            (new Argument('init'))->required(false)->argumentValueRequired(false),
            (new Argument('rollback'))->required(false)->argumentValueRequired(false),
        ];
    }

    public function execute(): int{
        // rollback引数を取得
        $rollback = $this->getArgumentValue('rollback');

        // init引数があれば、createMigrationsTable()を呼び出す
        if($this->getArgumentValue('init')){
            $this->createMigrationsTable();
        }

        // rollback引数があれば、rollback()を呼び出す
        if ($rollback) {
            $rollbackN = $rollback === true ? 1 : (int)$rollback;
            $this->rollback($rollbackN);
        } else {
            $this->migrate();
        }
        return 0;
    }

    private function createMigrationsTable(){
        $mysqli = new MySQLWrapper();
        $result = $mysqli->query("CREATE TABLE IF NOT EXISTS migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            filename VARCHAR(255) NOT NULL
        );");

        if ($result === false) {
            throw new Exception("Failed to create migrations table");
        }
    }

    private function migrate(){
        $lastMigration = $this->getLastMigration();
        $allMigrationFiles = $this->getAllMigrationFiles();

        $currentIndex = ($lastMigration) ? array_search($lastMigration, $allMigrationFiles) : 0;

        for ($i = $currentIndex; $i < count($allMigrationFiles); $i++) {
            $filename = $allMigrationFiles[$i];
            include_once($filename);
            $migrationClass = $this->getClassnameFromMigrationFilename($filename);
            $migration = new $migrationClass();
            $queries = $migration->up();

            if (empty($queries)) throw new Exception("Migration file " . $filename . " does not have any queries");

            $this->processQueries($queries);
            $this->insertMigration($filename);
        }
    }

    private function getLastMigration(){
        $mysqli = new MySQLWrapper();
        $result = $mysqli->query("SELECT filename FROM migrations ORDER BY id DESC LIMIT 1;");
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc()['filename'];
        }
        return null;
    }

    private function getAllMigrationFiles(string $order = 'asc'){
        $allFiles = glob(__DIR__ . '/../../Database/Migration/*.php');
        usort($allFiles, function($a, $b) use ($order) {
            return $order === 'asc' ? strcmp($a, $b) : strcmp($b, $a);
        });
        return $allFiles;
    }

    private function getClassnameFromMigrationFilename(string $filename){
        if (preg_match('/([^_]+)\.php$/', $filename, $matches)) {
            return sprintf("%s\%s", 'Database\Migration', $matches[1]);
        }
        return null;
    }

    private function processQueries(array $queries){
        $mysqli = new MySQLWrapper();
        foreach ($queries as $query) {
            $result = $mysqli->query($query);
            if ($result === false) {
                throw new Exception("Failed to execute query: " . $query);
            } else {
                echo "Query executed successfully: " . $query . "\n";
            }
        }
    }

    private function insertMigration(string $filename){
        $mysqli = new MySQLWrapper();
        $query = "INSERT INTO migrations (filename) VALUES (?)";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("s", $filename);
        $stmt->execute();
        $stmt->close();
    }

    private function rollback(){
        $lastMigration = $this->getLastMigration();
        if($lastMigration === null) throw new Exception("No migrations found");
        $allMigrationFiles = $this->getAllMigrationFiles('desc');
        $currentIndex = array_search($lastMigration, $allMigrationFiles);

        for ($i = $currentIndex; $i < count($allMigrationFiles); $i++) {
            $filename = $allMigrationFiles[$i];
            include_once($filename);
            $migrationClass = $this->getClassnameFromMigrationFilename($filename);
            $migration = new $migrationClass();
            $queries = $migration->down();

            if (empty($queries)) throw new Exception("Migration file " . $filename . " does not have any queries");

            $this->processQueries($queries);
            $this->removeMigration($filename);
        }
    }

    private function removeMigration(string $filename){
        $mysqli = new MySQLWrapper();
        $query = "DELETE FROM migrations WHERE filename = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("s", $filename);
        $stmt->execute();
        $stmt->close();
    }


}