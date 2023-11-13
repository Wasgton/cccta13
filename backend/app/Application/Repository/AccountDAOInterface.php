<?php

namespace App\Application\Repository;

use App\Domain\Entities\Account;

interface AccountDAOInterface
{
    public function save(Account $account);
    public function getById(string $id) : Account|null;

}
