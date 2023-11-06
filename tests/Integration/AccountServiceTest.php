<?php

namespace Tests\Integration;

use App\Application\Exceptions\EmailAlreadyRegisteredException;
use App\Application\Exceptions\InvalidCPFException;
use App\Application\Exceptions\InvalidEmailException;
use App\Application\Exceptions\InvalidNameException;
use App\Application\Exceptions\InvalidPlateException;
use App\Application\Gateway\MailerGateway;
use App\Application\UseCases\GetAccount;
use App\Application\UseCases\SignUp;
use App\Domain\Entities\Account;
use App\Infra\Repository\AccountDAO;
use PHPUnit\Framework\MockObject\Exception;
use Tests\TestCase;

class AccountServiceTest extends TestCase
{
    /**
     * @throws Exception
     * @throws EmailAlreadyRegisteredException
     */
    public function test_should_create_a_passenger(): void
    {
        $passengerData = [
            'name' => $this->faker->firstName.' '.$this->faker->lastName,
            'email' => $this->faker->email,
            'cpf' => $this->faker->cpf(false),
            'isPassenger' => 1,
        ];
        $accountDAOStub = $this->createConfiguredStub(
            AccountDAO::class,
            [
                'save' => '',
                'getById' => Account::create(
                    $passengerData['name'],
                    $passengerData['email'],
                    $passengerData['cpf'],
                    null,
                    1,
                    0,
                ),
                'getByEmail' => null,
            ]
        );
        $account = (new SignUp($accountDAOStub))->execute($passengerData);
        $this->assertNotEmpty($account->accountId);
        $this->assertNotEmpty($account->verificationCode);
        $this->assertEquals($passengerData['name'], $account->name);
        $this->assertEquals($passengerData['email'], $account->email);
        $this->assertEquals($passengerData['cpf'], $account->cpf);
    }

    public function test_should_send_a_email_after_create_a_passenger(): void
    {
        $passengerData = [
            'name' => $this->faker->firstName.' '.$this->faker->lastName,
            'email' => $this->faker->email,
            'cpf' => $this->faker->cpf(false),
            'isPassenger' => 1,
        ];
        $accountDAOStub = $this->createConfiguredStub(
            AccountDAO::class,
            [
                'save' => '',
                'getById' => Account::create(
                    $passengerData['name'],
                    $passengerData['email'],
                    $passengerData['cpf'],
                    null,
                    1,
                    0,
                ),
                'getByEmail' => null,
            ]
        );
        $mailerSpy = $this->createMock(MailerGateway::class);
        $mailerSpy->expects($this->once())->method('send')->willReturn(true);
        $response = (new SignUp($accountDAOStub, $mailerSpy))->execute($passengerData);
    }

    public function test_should_not_create_a_passenger_if_email_is_already_registered()
    {
        $firstPassengerData = [
            'name' => $this->faker->firstName.' '.$this->faker->lastName,
            'email' => $this->faker->email,
            'cpf' => $this->faker->cpf(false),
            'isPassenger' => 1,
        ];
        $signUp = new SignUp();
        $signUp->execute($firstPassengerData);

        $secondPassengerData = [
            'name' => $this->faker->firstName.' '.$this->faker->lastName,
            'email' => $firstPassengerData['email'],
            'cpf' => $this->faker->cpf(false),
            'isPassenger' => 1,
        ];
        $this->expectException(EmailAlreadyRegisteredException::class);
        $signUp->execute($secondPassengerData);
    }

    public function test_should_not_create_a_passenger_with_invalid_email()
    {
        $firstPassengerData = [
            'name' => $this->faker->firstName.' '.$this->faker->lastName,
            'email' => $this->faker->freeEmailDomain,
            'cpf' => $this->faker->cpf(false),
            'isPassenger' => 1,
        ];
        $this->expectException(InvalidEmailException::class);
        $passengers = (new SignUp())->execute($firstPassengerData);
    }

    public function test_should_not_create_a_passenger_with_invalid_cpf()
    {
        $firstPassengerData = [
            'name' => $this->faker->firstName.' '.$this->faker->lastName,
            'email' => $this->faker->safeEmail,
            'cpf' => '98765432122',
            'isPassenger' => 1,
        ];
        $this->expectException(InvalidCPFException::class);
        $passengers = (new SignUp())->execute($firstPassengerData);
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
        $passengers = (new SignUp())->execute($firstPassengerData);
    }

    public function test_should_create_a_driver()
    {
        $passengerData = [
            'name' => $this->faker->firstName.' '.$this->faker->lastName,
            'email' => $this->faker->email,
            'cpf' => $this->faker->cpf(false),
            'isDriver' => 1,
            'plate' => 'ABC1234'
        ];
        $account = (new SignUp())->execute($passengerData);
        $this->assertNotEmpty($account->accountId);
        $this->assertNotEmpty($account->verificationCode);
    }

    public function test_should_not_create_a_driver_with_invalid_plate()
    {
        $firstPassengerData = [
            'name' => $this->faker->firstName.' '.$this->faker->lastName,
            'email' => $this->faker->safeEmail,
            'cpf' => $this->faker->cpf(false),
            'isDriver' => 1,
            'plate' => 'ABC-1234',
        ];
        $this->expectException(InvalidPlateException::class);
        (new SignUp())->execute($firstPassengerData);
    }

    public function test_should_get_account_by_id()
    {
        $passengerData = [
            'name' => $this->faker->firstName.' '.$this->faker->lastName,
            'email' => $this->faker->email,
            'cpf' => $this->faker->cpf(false),
            'isPassenger' => 1,
        ];
        $accountOuput = (new SignUp())->execute($passengerData);
        $account = (new GetAccount())->execute($accountOuput->accountId);
        $this->assertEquals($accountOuput->accountId, $account->accountId);
        $this->assertEquals($accountOuput->name, $account->name);
        $this->assertEquals($accountOuput->email, $account->email);
        $this->assertEquals($accountOuput->cpf, $account->cpf);
    }

}
