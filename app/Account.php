<?php

namespace App;

use App\Exceptions\InvalidCPFException;
use App\Exceptions\InvalidEmailException;
use App\Exceptions\InvalidNameException;
use App\Exceptions\InvalidPlateException;
use Ramsey\Uuid\Uuid;

class Account
{

    private function __construct(
        readonly string|null $accountId = null,
        readonly string $name,
        readonly string $email,
        readonly string $cpf,
        readonly string|null $carPlate,
        readonly int $isPassenger,
        readonly int $isDriver,
        readonly string $verificationCode,
        readonly string $date
    ){}

    public static function create($name, $email, $cpf, $carPlate, $isPassenger, $isDriver): Account
    {
        if (!preg_match('/^[a-z0-9.]+@[a-z0-9]+\.[a-z]+(\.[a-z]+)?$/i', $email)) {
            throw new InvalidEmailException();
        }
        $cpfValidator = new CPFValidator($cpf);
        if (!$cpfValidator->validate()) {
            throw new InvalidCPFException();
        }
        if (!preg_match('/[a-zA-Z] [a-zA-Z]+/', $name)) {
            throw new InvalidNameException();
        }
        if(isset($carPlate) && !preg_match('/^[a-zA-Z]{3}\d{4}$/', $carPlate)) {
            throw new InvalidPlateException();
        }
        $accountId = Uuid::uuid4()->toString();
        $verificationCode = Uuid::uuid4()->toString();
        $date = date('Y-m-d H:i:s');
        return new Account(
            $accountId,
            $name,
            $email,
            $cpf,
            $carPlate ?? null,
            $isPassenger ?? 0,
            $isDriver ?? 0,
            $verificationCode,
            $date
        );
    }

    public static function restore($accountId, $name, $email, $cpf, $carPlate, $isPassenger, $isDriver, $verificationCode, $date) : Account
    {
        return new Account(
            $accountId,
            $name,
            $email,
            $cpf,
            $carPlate ?? null,
            $isPassenger ?? 0,
            $isDriver ?? 0,
            $verificationCode,
            $date
        );
    }


}
