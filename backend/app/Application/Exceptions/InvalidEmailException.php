<?php

namespace App\Application\Exceptions;

use Throwable;

class InvalidEmailException extends \Exception
{
    protected $message = 'Invalid Email!';

    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}
