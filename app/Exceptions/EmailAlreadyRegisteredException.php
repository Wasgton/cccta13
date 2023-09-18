<?php

namespace App\Exceptions;

use Throwable;

class EmailAlreadyRegisteredException extends \Exception
{
    protected $message = 'Email is already registered';

    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}
