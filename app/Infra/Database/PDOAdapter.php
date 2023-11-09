<?php

namespace App\Infra\Database;

use App\Application\Exceptions\SQLException;
use PDO;
use PDOException;

class PDOAdapter implements ConnectionInterface {

    private $host;
    private $username;
    private $port;
    private $password;
    private $database;
    private $pdo;


    public function __construct()
    {
        $this->host = 'mysql';
        $this->username = 'root';
        $this->password = '';
        $this->port = "3306";
        $this->database = 'cccat_13';
        $this->connect();
    }

    public function connect() {
        $connectionString = "mysql:host={$this->host};dbname={$this->database};port={$this->port};charset=UTF8";

        try {
            $this->pdo = new PDO(
                $connectionString, $this->username, $this->password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
                ]
            );
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
            throw new SQLException($e->getMessage());
        }
    }

    public function escapeString($string) {
        return $this->pdo->quote($string);
    }

    public function close() {
        $this->pdo = null;
    }
}
