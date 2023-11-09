<?php

namespace App\Application\UseCases;

use App\Application\Exceptions\EmailAlreadyRegisteredException;
use App\Application\Gateway\MailerGateway;
use App\Application\Repository\AccountDAOInterface;
use App\Domain\Entities\Account;
use App\Infra\Gateway\MailerInterface;
use App\Infra\Repository\AccountDAO;

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
            $input['plate']??null,
            $input['isPassenger']??null,
            $input['isDriver']??null
        );
        $this->accountDAO->save($account);
        $this->mailerGateway->send($account->email);
        return $this->accountDAO->getById($account->accountId);
    }

}
