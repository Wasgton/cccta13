<?php

namespace App\DAO;

use App\Account;

interface AccountDAOInterface
{
    public function save(Account $account);
    public function getById(string $id) : Account|null;

}
