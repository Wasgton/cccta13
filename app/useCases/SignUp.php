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

class SignUp
{
    public function __construct(
        private AccountDAOInterface $accountDAO = new AccountDAO(),
        private MailerInterface $mailerGateway = new MailerGateway(),
    ){}

    /**
     * @throws EmailAlreadyRegisteredException
     */
    public function execute(array $input)
    {
        $account = $this->accountDAO->getByEmail($input['email']);
        if(!is_null($account)) {
            throw new EmailAlreadyRegisteredException();
        }
        $account = Account::create(
            $input['name'],
            $input['email'],
            $input['cpf'],
            $input['plate'],
            $input['isPassenger'],
            $input['isDriver']
        );
        $this->accountDAO->save($account);
        $this->mailerGateway->send($account->email);
        return $this->accountDAO->getById($account->accountId);
    }

}
