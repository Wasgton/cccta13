<?php

namespace App\Application\Exceptions;

use Throwable;

class AcceptRideNotAllowed extends \Exception
{
    protected $message = 'Accept Ride is not allowed';

    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}
