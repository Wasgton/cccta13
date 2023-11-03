<?php

namespace App\useCases;

use App\Account;
use App\CPFValidator;
use App\DAO\AccountDAO;
use App\DAO\AccountDAOInterface;
use App\MailerGateway;
use App\Exceptions\EmailAlreadyRegisteredException;
use App\Exceptions\InvalidCPFException;
use App\Exceptions\InvalidEmailException;
use App\Exceptions\InvalidNameException;
use App\Exceptions\InvalidPlateException;
use App\MailerInterface;
use config\Connection;
use PDO;
use Ramsey\Uuid\Uuid;

class GetAccount
{
    public function __construct(
        private AccountDAOInterface $dao = new AccountDAO(),
    ){}

    public function execute($accountId)
    {
        return $this->dao->getById($accountId);
    }

}
