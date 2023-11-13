<?php

namespace App\Infra\Repository;

use App\Application\Repository\RideDAOInterface;
use App\Domain\Entities\Ride;
use App\Infra\Database\PDOAdapter;
use PDO;

class RideDAO implements RideDAOInterface
{

    public function save(Ride $ride)
    {
        return (new PDOAdapter())->query(
            "INSERT INTO ride (ride_id,passenger_id, from_lat, from_long, to_lat, to_long, status, date)
                VALUES (:rideId,:passengerId,:fromLat,:fromLong,:toLat,:toLong,:status, :date)",
            [
                'rideId' => $ride->rideId,
                'passengerId' => $ride->passengerId,
                'fromLat' => $ride->fromLat,
                'fromLong' => $ride->fromLong,
                'toLat' => $ride->toLat,
                'toLong' => $ride->toLong,
                'status' => $ride->getStatus(),
                'date' => $ride->date
            ]
        );
    }

    public function getRideById(string $rideId) : Ride|null
    {
        $data = (new PDOAdapter())->query(
            "SELECT * FROM ride WHERE ride_id = :ride_id",
            ['ride_id' => $rideId]
        )->fetchAll(PDO::FETCH_ASSOC);
        if(empty($data)){
            return null;
        }
        $data = [$data];
        return Ride::restore(
            $data['ride_id'],
            $data['passenger_id'],
            $data['driver_id'],
            (double) $data['from_lat'],
            (double) $data['from_long'],
            (double) $data['to_lat'],
            (double) $data['to_long'],
            $data['status'],
            $data['date']
        );
    }

    public function getActiveRidesByPassengerId($passengerId)
    {
        return (new PDOAdapter())->query(
            "SELECT *
                 FROM ride
                 WHERE passenger_id = :passenger_id
                 AND status <> 'completed'",
            ['passenger_id' => $passengerId]
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update(Ride $ride)
    {
        return (new PDOAdapter())->query(
            "UPDATE ride
                 SET status = :status,
                     driver_id = :driver_id
                 WHERE ride_id = :ride_id",
            [
                'driver_id' => $ride->getDriverId(),
                'status' => $ride->getStatus(),
                'ride_id' => $ride->rideId
            ]
        );
    }

    public function getActiveRidesByDriverId($driver_id)
    {
        return (new PDOAdapter())->query(
            "SELECT *
                 FROM ride
                 WHERE driver_id = :driver_id
                 AND status <> 'completed'",
            ['driver_id' => $driver_id]
        )->fetchAll(PDO::FETCH_ASSOC);
    }
}
