<?php

namespace App\Application\Exceptions;

use Throwable;

class SQLException extends \Exception
{
    protected $message = '';

    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}
