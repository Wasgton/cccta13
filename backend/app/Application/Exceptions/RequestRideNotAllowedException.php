<?php

namespace App\Application\Exceptions;

use Throwable;

class RequestRideNotAllowedException extends \Exception
{
    protected $message = 'Request Ride is not allowed';

    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}
