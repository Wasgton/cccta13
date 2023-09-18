<?php

namespace App\Services;

use App\CPFValidator;
use App\Exceptions\EmailAlreadyRegisteredException;
use App\Exceptions\InvalidCPFException;
use App\Exceptions\InvalidEmailException;
use App\Exceptions\InvalidNameException;
use config\Connection;
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
        if (!preg_match('/^[a-zA-Z] [a-zA-Z]/i', $input['name'])) {
            throw new InvalidNameException();
        }

        $input['account_id'] = Uuid::uuid4()->toString();
        $input['verification_code'] = Uuid::uuid4()->toString();
        $connection = new Connection();

        $account = $connection->query(
            "SELECT * FROM account WHERE email = :email",
            ['email' => $input['email']]
        );
        if($account->rowCount() > 0) {
            throw new EmailAlreadyRegisteredException();
        }
        $connection->query(
            "INSERT INTO account (account_id, name, email, cpf, is_passenger, verification_code)
                 VALUES (:account_id, :name, :email, :cpf, :isPassenger, :verification_code)",
            $input
        );
        
        
        
        return $input;
    }

    private function sendEmail()
    {
        return true;
    }

}
