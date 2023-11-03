<?php

namespace App;

use App\Exceptions\AcceptRideNotAllowed;
use Ramsey\Uuid\Uuid;

class Ride
{
    private function __construct(
        readonly string|null $rideId = null,
        readonly string $passengerId,
        private string|null $driverId = null,
        readonly string $fromLat,
        readonly string $fromLong,
        readonly string $toLat,
        readonly string $toLong,
        private string $status,
        readonly string $date
    ){}

    public static function create($passengerId, $fromLat, $fromLong, $toLat, $toLong) : Ride
    {
        $ride_id = Uuid::uuid4()->toString();
        $status = 'requested';
        $date = date('Y-m-d H:i:s');
        return new Ride(
            $ride_id,
            $passengerId,
            null,
            number_format($fromLat, 14),
            number_format($fromLong, 14),
            number_format($toLat, 14),
            number_format($toLong, 14),
            $status,
            $date
        );
    }

    public static function restore($rideId, $passengerId, $driverId, $fromLat, $fromLong, $toLat, $toLong, $status, $date) : Ride
    {
        return new Ride (
            $rideId,
            $passengerId,
            $driverId,
            number_format($fromLat, 14),
            number_format($fromLong, 14),
            number_format($toLat, 14),
            number_format($toLong, 14),
            $status,
            $date
        );
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function accept(string $driver_id)
    {
        if ($this->status !== 'requested') {
            throw new AcceptRideNotAllowed("Ride wasn't requested");
        }
        $this->status = 'accepted';
        $this->driverId = $driver_id;
    }

    public function getDriverId()
    {
        return $this->driverId;
    }

}
