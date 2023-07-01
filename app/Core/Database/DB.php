<?php

namespace App\Core\Database;

use PDO;
use PDOException;

class DB
{
    private static self $instance;

    private PDO $PDO;
    private array $dbConfig;

    private function __construct()
    {
        $this->dbConfig = require_once __DIR__ . '/config.php';
    }

    public static function init(): DB
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @return bool|$this
     * @throws PDOException
     */
    private function connect(): static|bool
    {
        $this->PDO = new PDO(
            "{$this->dbConfig['driver']}:" .
            "host={$this->dbConfig['host']};" .
            "dbname={$this->dbConfig['database']}",
            $this->dbConfig['username'],
            $this->dbConfig['password']
        );

        return $this;
    }

    public function execute(string $query, array $values, bool $fatch = false, bool $returnId = false): string|bool|array
    {
        try {
            $this->connect();
            $this->PDO->beginTransaction();

            $this->PDO->exec("SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");

            $sth = $this->PDO->prepare($query);
            if (!$sth->execute($values)) {
                $this->PDO->rollBack();
                return false;
            }

            $result = true;
            if ($fatch) {
                $result = $sth->fetchAll($this->PDO::FETCH_ASSOC);
            }
            if ($returnId) {
                $result = $this->PDO->lastInsertId();
            }

            $this->PDO->commit();

            return $result;
        } catch (\Exception $e) {
            $this->PDO->rollBack();
            throw new \Exception("Erro ao executar: {$query} {$e->getMessage()} {$e->getTraceAsString()}");
        }
    }

}