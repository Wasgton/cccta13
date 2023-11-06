<?php

namespace Tests\Integration;

use App\Domain\Entities\Account;
use App\Infra\Repository\AccountDAO;
use Tests\TestCase;

class AccountDAOTest extends TestCase
{
    public function test_should_create_a_register_on_account_table_and_get_by_email()
    {
        $accountData = [
            'name' => $this->faker->firstName.' '.$this->faker->lastName,
            'email' => $this->faker->email,
            'cpf' => $this->faker->cpf(false),
            'carPlate' => null,
            'isPassenger' => 1,
            'isDriver' => 0,
        ];
        $account = Account::create(...$accountData);
        (new AccountDAO())->save($account);
        $savedAccount = (new AccountDAO())->getByEmail($accountData['email']);
        $this->assertEquals($account->accountId, $savedAccount->accountId);
        $this->assertEquals($account->name, $savedAccount->name);
        $this->assertEquals($account->email, $savedAccount->email);
        $this->assertEquals($account->cpf, $savedAccount->cpf);
    }

    public function test_should_create_a_register_on_account_table_and_get_by_account_id()
    {
        $accountData = [
            'name' => $this->faker->firstName.' '.$this->faker->lastName,
            'email' => $this->faker->email,
            'cpf' => $this->faker->cpf(false),
            'carPlate' => null,
            'isPassenger' => 1,
            'isDriver' => 0,
        ];
        $account = Account::create(...$accountData);
        (new AccountDAO())->save($account);
        $savedAccount = (new AccountDAO())->getById($account->accountId);
        $this->assertEquals($account->accountId, $savedAccount->accountId);
        $this->assertEquals($account->name, $savedAccount->name);
        $this->assertEquals($account->email, $savedAccount->email);
        $this->assertEquals($account->cpf, $savedAccount->cpf);
    }

}