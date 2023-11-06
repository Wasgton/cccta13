<?php

namespace App\Application\Repository;

use App\Domain\Entities\Ride;

interface RideDAOInterface
{
    public function save(Ride $ride);
    public function getRideById(string $rideId);

    public function getActiveRidesByPassengerId(string $passengerId);
    public function getActiveRidesByDriverId(string $driver_id);
}
