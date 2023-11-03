<?php

namespace App\DAO;

use App\Ride;
use Ramsey\Uuid\Uuid;

interface RideDAOInterface
{
    public function save(Ride $ride);
    public function getRideById(string $rideId);

    public function getActiveRidesByPassengerId(string $passengerId);
    public function getActiveRidesByDriverId(string $driver_id);
}
