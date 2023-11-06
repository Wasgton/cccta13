<?php

namespace App\Application\UseCases;

use App\Infra\Repository\RideDAO;

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
