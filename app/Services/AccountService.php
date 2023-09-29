<?php

namespace App\Services;

use App\CPFValidator;
use App\DAO\AccountDAO;
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
    public AccountDAO $dao;
    public function __construct()
    {
        $this->dao = new AccountDAO();
    }

    /**
     * @throws EmailAlreadyRegisteredException
     */
    public function signUp(array $input)
    {
        $input['account_id'] = Uuid::uuid4()->toString();
        $input['verification_code'] = Uuid::uuid4()->toString();
        $input['isPassenger'] = $input['isPassenger'] ?? 0;
        $input['isDriver'] = $input['isDriver'] ?? 0;
        $input['plate'] = $input['plate'] ?? null;
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
        if(isset($input['plate']) && !preg_match('/^[a-zA-Z]{3}\d{4}$/', $input['plate'])) {
            throw new InvalidPlateException();
        }
        $account = $this->dao->getByEmail($input['email']);
        if(count($account)) {
            throw new EmailAlreadyRegisteredException();
        }
        $this->dao->save($input);
        return $this->dao->getById($input['account_id']);
    }

    public function getAccount($accountId)
    {
        return $this->dao->getById($accountId);
    }

//    private function sendEmail()
////    {
////        return true;
////    }


}
