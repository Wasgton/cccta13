<?php

namespace App\useCases;

use App\DAO\AccountDAO;
use App\DAO\RideDAO;
use App\Exceptions\AcceptRideNotAllowed;
use App\Exceptions\RequestRideNotAllowedException;
use App\Ride;
use Ramsey\Uuid\Uuid;

class GetRide
{
    public function __construct(
        public RideDAO $rideDAO = new RideDAO()
    ){}

    public function execute($ride_id)
    {
        return (new RideDAO())->getRideById($ride_id);
    }

}
