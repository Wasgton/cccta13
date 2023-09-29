<?php

namespace App\DAO;

use config\Connection;
use PDO;
use PDOStatement;

class AccountDAO
{

    public function save(array $accountData) : PDOStatement
    {
        return (new Connection())->query(
            "INSERT INTO account (account_id, name, email, cpf, car_plate, is_passenger, is_driver, verification_code)
                 VALUES (:account_id, :name, :email, :cpf, :plate, :isPassenger, :isDriver, :verification_code)",
            $accountData
        );
    }

    public function getById($accountId) : array
    {
        $account = (new Connection())->query(
            "SELECT * FROM account WHERE account_id = :account_id",
            ['account_id' => $accountId]
        )->fetchAll(PDO::FETCH_ASSOC);
        return reset($account);
    }

    public function getByEmail($email) : array
    {
        return (new Connection())->query(
            "SELECT * FROM account WHERE email = :email",
            ['email' => $email]
        )->fetchAll(PDO::FETCH_ASSOC);
    }

}
