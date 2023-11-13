<?php

namespace App\Application\Exceptions;

use Throwable;

class InvalidPlateException extends \Exception
{
    protected $message = 'Invalid Plate Format!';

    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}
