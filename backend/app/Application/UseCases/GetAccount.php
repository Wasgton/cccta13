<?php

namespace App\Application\UseCases;

use App\Application\Repository\AccountDAOInterface;
use App\Infra\Repository\AccountDAO;

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
