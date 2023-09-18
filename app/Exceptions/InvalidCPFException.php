<?php

namespace App\Exceptions;

use Throwable;

class InvalidCPFException extends \Exception
{
    protected $message = 'Invalid CPF!';

    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}
