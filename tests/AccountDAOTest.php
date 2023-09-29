<?php

namespace Tests;

use App\DAO\AccountDAO;

class AccountDAOTest extends TestCase
{
    public function test_should_create_a_register_on_account_table_and_get_by_email()
    {
        $accountData = [
            'account_id' => $this->faker->uuid,
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'cpf' => $this->faker->cpf(false),
            'isPassenger' => 1,
            'verification_code' => $this->faker->uuid,
            'isDriver' => 0,
            'plate' => null,
        ];
        (new AccountDAO())->save($accountData);
        $savedAccount = (new AccountDAO())->getByEmail($accountData['email']);
        $this->assertCount(1, $savedAccount);
        $savedAccount = reset($savedAccount);
        $this->assertEquals($accountData['name'], $savedAccount['name']);
        $this->assertEquals($accountData['email'], $savedAccount['email']);
        $this->assertEquals($accountData['cpf'], $savedAccount['cpf']);
    }

    public function test_should_create_a_register_on_account_table_and_get_by_account_id()
    {
        $accountData = [
            'account_id' => $this->faker->uuid,
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'cpf' => $this->faker->cpf(false),
            'isPassenger' => 1,
            'verification_code' => $this->faker->uuid,
            'isDriver' => 0,
            'plate' => null,
        ];
        (new AccountDAO())->save($accountData);
        $savedAccount = (new AccountDAO())->getById($accountData['account_id']);
        $this->assertEquals($accountData['account_id'], $savedAccount['account_id']);
        $this->assertEquals($accountData['name'], $savedAccount['name']);
        $this->assertEquals($accountData['email'], $savedAccount['email']);
        $this->assertEquals($accountData['cpf'], $savedAccount['cpf']);
    }

}