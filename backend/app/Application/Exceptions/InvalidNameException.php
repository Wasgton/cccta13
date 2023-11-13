<?php

namespace App\Application\Exceptions;

use Throwable;

class InvalidNameException extends \Exception
{
    protected $message = 'Invalid Name!';

    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}
