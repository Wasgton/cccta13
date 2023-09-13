<?php

namespace Config;

class Connection {

    private $pdo;

    public function __construct(
        private $host = 'localhost',
        private $username = 'root',
        private $password = '',
        private $database = 'cccat_13'
    )
    {
        $this->connect();
    }

    public function connect() {
        $dsn = "mysql:host={$this->host};dbname={$this->database}";

        try {
            $this->pdo = new PDO($dsn, $this->username, $this->password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    public function query($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            die("Query failed: " . $e->getMessage());
        }
    }

    public function escapeString($string) {
        return $this->pdo->quote($string);
    }

    public function close() {
        $this->pdo = null;
    }
}
