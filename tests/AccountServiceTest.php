<?php

namespace Tests;

use App\DAO\AccountDAO;
use App\Exceptions\EmailAlreadyRegisteredException;
use App\Exceptions\InvalidCPFException;
use App\Exceptions\InvalidEmailException;
use App\Exceptions\InvalidNameException;
use App\Exceptions\InvalidPlateException;
use App\MailerGateway;
use App\Services\AccountService;
use PHPUnit\Framework\MockObject\Exception;
use Ramsey\Uuid\Uuid;

class AccountServiceTest extends TestCase
{
    /**
     * @throws Exception
     * @throws EmailAlreadyRegisteredException
     */
    public function test_should_create_a_passenger(): void
    {
        $passengerData = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'cpf' => $this->faker->cpf(false),
            'isPassenger' => 1,
        ];
        $accountDAOStub = $this->createConfiguredStub(
            AccountDAO::class,
            [
                'save' => '',
                'getById' => array_merge($passengerData,[
                    'account_id' => Uuid::uuid4()->toString(),
                    'verification_code' => Uuid::uuid4()->toString(),
                ]),
                'getByEmail' => [],
            ]
        );
        $response = (new AccountService($accountDAOStub))->signUp($passengerData);
        $this->assertNotEmpty($response['account_id']);
        $this->assertNotEmpty($response['verification_code']);
        $this->assertEquals($passengerData['name'], $response['name']);
        $this->assertEquals($passengerData['email'], $response['email']);
        $this->assertEquals($passengerData['cpf'], $response['cpf']);
    }

    public function test_should_send_a_email_after_create_a_passenger(): void
    {
        $passengerData = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'cpf' => $this->faker->cpf(false),
            'isPassenger' => 1,
        ];
        $accountDAOStub = $this->createConfiguredStub(
            AccountDAO::class,
            [
                'save' => '',
                'getById' => array_merge($passengerData,[
                    'account_id' => Uuid::uuid4()->toString(),
                    'verification_code' => Uuid::uuid4()->toString(),
                ]),
                'getByEmail' => [],
            ]
        );
        $mailerSpy = $this->createMock(MailerGateway::class);
        $mailerSpy->expects($this->once())->method('send')->willReturn(true);
        $response = (new AccountService($accountDAOStub, $mailerSpy))->signUp($passengerData);
    }

    public function test_should_not_create_a_passenger_if_email_is_already_registered()
    {
        $firstPassengerData = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'cpf' => $this->faker->cpf(false),
            'isPassenger' => 1,
        ];
        $accountService = new AccountService();
        $accountService->signUp($firstPassengerData);

        $secondPassengerData = [
            'name' => $this->faker->name,
            'email' => $firstPassengerData['email'],
            'cpf' => $this->faker->cpf(false),
            'isPassenger' => 1,
        ];
        $this->expectException(EmailAlreadyRegisteredException::class);
        $accountService->signUp($secondPassengerData);
    }

    public function test_should_not_create_a_passenger_with_invalid_email()
    {
        $firstPassengerData = [
            'name' => $this->faker->name,
            'email' => $this->faker->freeEmailDomain,
            'cpf' => $this->faker->cpf(false),
            'isPassenger' => 1,
        ];
        $this->expectException(InvalidEmailException::class);
        $passengers = (new AccountService())->signUp($firstPassengerData);
    }

    public function test_should_not_create_a_passenger_with_invalid_cpf()
    {
        $firstPassengerData = [
            'name' => $this->faker->name,
            'email' => $this->faker->safeEmail,
            'cpf' => '98765432122',
            'isPassenger' => 1,
        ];
        $this->expectException(InvalidCPFException::class);
        $passengers = (new AccountService())->signUp($firstPassengerData);
    }

    public function test_should_not_create_a_passenger_with_invalid_name()
    {
        $firstPassengerData = [
            'name' => $this->faker->firstName,
            'email' => $this->faker->safeEmail,
            'cpf' => $this->faker->cpf(false),
            'isPassenger' => 1,
        ];
        $this->expectException(InvalidNameException::class);
        $passengers = (new AccountService())->signUp($firstPassengerData);
    }

    public function test_should_create_a_driver()
    {
        $passengerData = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'cpf' => $this->faker->cpf(false),
            'isDriver' => 1,
            'plate' => 'ABC1234'
        ];
        $response = (new AccountService())->signUp($passengerData);
        $this->assertNotEmpty($response['account_id']);
        $this->assertNotEmpty($response['verification_code']);
    }

    public function test_should_not_create_a_driver_with_invalid_plate()
    {
        $firstPassengerData = [
            'name' => $this->faker->name,
            'email' => $this->faker->safeEmail,
            'cpf' => $this->faker->cpf(false),
            'isDriver' => 1,
            'plate' => 'ABC-1234',
        ];
        $this->expectException(InvalidPlateException::class);
        $passengers = (new AccountService())->signUp($firstPassengerData);
    }

    public function test_should_get_account_by_id()
    {
        $passengerData = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'cpf' => $this->faker->cpf(false),
            'isPassenger' => 1,
        ];
        $account = (new AccountService())->signUp($passengerData);
        $response = (new AccountService())->getAccount($account['account_id']);
        $this->assertEquals($account['account_id'], $response['account_id']);
        $this->assertEquals($account['name'], $response['name']);
        $this->assertEquals($account['email'], $response['email']);
        $this->assertEquals($account['cpf'], $response['cpf']);
    }
}
