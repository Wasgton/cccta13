<?php

namespace App\Infra\Repository;

use App\Application\Repository\AccountDAOInterface;
use App\Domain\Entities\Account;
use config\Connection;
use PDO;

class AccountDAO implements AccountDAOInterface
{

    public function save(Account $account)
    {
        return (new Connection())->query(
            "INSERT INTO account (account_id, name, email, cpf, car_plate, is_passenger, is_driver, verification_code)
                 VALUES (:account_id, :name, :email, :cpf, :plate, :isPassenger, :isDriver, :verification_code)",
            [
                'account_id'=>$account->accountId,
                'name'=>$account->name,
                'email'=>$account->email,
                'cpf'=>$account->cpf,
                'plate'=>$account->carPlate,
                'isPassenger'=>$account->isPassenger,
                'isDriver'=>$account->isDriver,
                'verification_code'=>$account->verificationCode
            ]
        );
    }

    public function getById(string $id) : Account|null
    {
        [$account] = (new Connection())->query(
            "SELECT * FROM account WHERE account_id = :account_id",
            ['account_id' => $id]
        )->fetchAll(PDO::FETCH_ASSOC);
        if (is_null($account)) {
            return null;
        }
        return Account::restore(
            $account['account_id'],
            $account['name'],
            $account['email'],
            $account['cpf'],
            $account['car_plate'],
            $account['is_passenger'],
            $account['is_driver'],
            $account['verification_code'],
            $account['date']
        );
    }

    public function getByEmail(string $email) : Account|null
    {
        [$account] = (new Connection())->query(
            "SELECT * FROM account WHERE email = :email",
            ['email' => $email]
        )->fetchAll(PDO::FETCH_ASSOC);
        if (is_null($account)) {
            return null;
        }
        return Account::restore(
            $account['account_id'],
            $account['name'],
            $account['email'],
            $account['cpf'],
            $account['car_plate'],
            $account['is_passenger'],
            $account['is_driver'],
            $account['verification_code'],
            $account['date']
        );
    }

}
