<?php

namespace App\DAO;

use Ramsey\Uuid\Uuid;

interface RideDAOInterface
{
    public function save(array $data);
    public function getRideById(Uuid $rideId);
}
