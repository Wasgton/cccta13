<?php

namespace App\useCases;

use App\DAO\AccountDAO;
use App\DAO\RideDAO;
use App\Exceptions\AcceptRideNotAllowed;
use App\Exceptions\RequestRideNotAllowedException;
use App\Ride;
use Ramsey\Uuid\Uuid;

class AcceptRide
{

    public function __construct(
        public RideDAO $rideDAO = new RideDAO(),
        public AccountDAO $accountDAO = new AccountDAO()
    ){}

    public function execute($data)
    {
        $driver = $this->accountDAO->getById($data['driver_id']);
        if (!is_null($driver) && !$driver->isDriver) {
            throw new AcceptRideNotAllowed('Account is not from a driver');
        }
        $ride = $this->rideDAO->getRideById($data['ride_id']);
        $driversRide = $this->rideDAO->getActiveRidesByDriverId($data['driver_id']);
        if (isset($driversRide) && count($driversRide)) {
            throw new AcceptRideNotAllowed('Driver is already in a ride');
        }
        $ride->accept($data['driver_id']);
        (new RideDAO())->update($ride);
    }

}
