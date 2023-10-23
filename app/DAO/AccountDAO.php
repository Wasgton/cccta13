<?php

namespace App\DAO;

use config\Connection;
use PDO;
use PDOStatement;

class AccountDAO implements AccountDAOInterface
{

    public function save(array $data)
    {
        return (new Connection())->query(
            "INSERT INTO account (account_id, name, email, cpf, car_plate, is_passenger, is_driver, verification_code)
                 VALUES (:account_id, :name, :email, :cpf, :plate, :isPassenger, :isDriver, :verification_code)",
            $data
        );
    }

    public function getById(string $id) : array
    {
        [$account] = (new Connection())->query(
            "SELECT * FROM account WHERE account_id = :account_id",
            ['account_id' => $id]
        )->fetchAll(PDO::FETCH_ASSOC);
        return $account;
    }

    public function getByEmail(string $email) : array
    {
        return (new Connection())->query(
            "SELECT * FROM account WHERE email = :email",
            ['email' => $email]
        )->fetchAll(PDO::FETCH_ASSOC);
    }

}
