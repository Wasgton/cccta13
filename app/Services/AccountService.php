<?php

namespace App\Services;

use Config\Connection;

class AccountService
{
    public function signUp(array $input)
    {
        $input['account_id'] = \Ramsey\Uuid\Uuid::uuid4()->toString();
        $input['verification_code'] = \Ramsey\Uuid\Uuid::uuid4()->toString();
        $connection = new Connection();
        $stmt = $connection->query(
            "INSERT INTO accounts (account_id, name, email, cpf, isPassenger, verification_code) 
                 VALUES (:account_id, :name, :email, :cpf, :isPassenger, :verification_code)",
            $input
        );
        return $input;
    }

}
