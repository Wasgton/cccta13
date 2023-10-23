<?php

namespace App\DAO;

interface AccountDAOInterface
{
    public function save(array $data);
    public function getById(string $id) : array;

}
