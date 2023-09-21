<?php

namespace App\Services;

use App\CPFValidator;
use App\Exceptions\EmailAlreadyRegisteredException;
use App\Exceptions\InvalidCPFException;
use App\Exceptions\InvalidEmailException;
use App\Exceptions\InvalidNameException;
use App\Exceptions\InvalidPlateException;
use config\Connection;
use PDO;
use Ramsey\Uuid\Uuid;

class AccountService
{
    /**
     * @throws EmailAlreadyRegisteredException
     */
    public function signUp(array $input)
    {
        if (!preg_match('/^[a-z0-9.]+@[a-z0-9]+\.[a-z]+(\.[a-z]+)?$/i', $input['email'])) {
            throw new InvalidEmailException();
        }
        $cpfValidator = new CPFValidator($input['cpf']);
        if (!$cpfValidator->validate()) {
            throw new InvalidCPFException();
        }
        if (!preg_match('/[a-zA-Z] [a-zA-Z]+/', $input['name'])) {
            throw new InvalidNameException();
        }
        if (!preg_match('/^[a-zA-Z]{3}\d{4}$/', $input['plate'])) {
            throw new InvalidPlateException();
        }
        $input['account_id'] = Uuid::uuid4()->toString();
        $input['verification_code'] = Uuid::uuid4()->toString();
        $account = $this->getAccountByEmail($input['email']);
        if(count($account)) {
            throw new EmailAlreadyRegisteredException();
        }
        $this->createAccount($input);
        $account = $this->getAccountById($input['account_id']);
        $this->sendEmail($account);
        return $account;
    }

    private function sendEmail()
    {
        return true;
    }

    private function getAccountById($accountId)
    {
        $account = (new Connection())->query(
            "SELECT * FROM account WHERE account_id = :account_id",
            ['account_id' => $accountId]
        )->fetchAll(PDO::FETCH_ASSOC);
        return reset($account);
    }

    private function getAccountByEmail($email)
    {
        return (new Connection())->query(
            "SELECT * FROM account WHERE email = :email",
            ['email' => $email]
        )->fetchAll();
    }

    private function createAccount($input)
    {
        $input['isPassenger'] = $input['isPassenger'] ?? 0;
        $input['isDriver'] = $input['isDriver'] ?? 0;
        $input['plate'] = $input['plate'] ?? null;

        return (new Connection())->query(
            "INSERT INTO account (account_id, name, email, cpf, car_plate, is_passenger, is_driver, verification_code)
                 VALUES (:account_id, :name, :email, :cpf, :plate, :isPassenger, :isDriver, :verification_code)",
            $input
        );
    }

}
