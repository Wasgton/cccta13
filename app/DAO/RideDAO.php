<?php

namespace App\DAO;

use config\Connection;
use PDO;

class RideDAO implements RideDAOInterface
{

    public function save($data)
    {
        return (new Connection())->query(
            "INSERT INTO ride (ride_id,passenger_id, from_lat, from_long, to_lat, to_long, status)
                 VALUES          (:ride_id,:passenger_id,:from_lat,:from_long,:to_lat,:to_long,:status)",
            $data
        );
    }

    public function getRideById($rideId)
    {
        [$ride] = (new Connection())->query(
            "SELECT * FROM ride WHERE ride_id = :ride_id",
            ['ride_id' => $rideId]
        )->fetchAll(PDO::FETCH_ASSOC);
        return $ride;
    }

    public function getActiveRidesByPassengerId(mixed $passengerId)
    {
        return (new Connection())->query(
            "SELECT *
                 FROM ride
                 WHERE passenger_id = :passenger_id
                 AND status <> 'completed'",
            ['passenger_id' => $passengerId]
        )->fetchAll(PDO::FETCH_ASSOC);
    }
}
