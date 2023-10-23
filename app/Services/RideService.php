<?php

namespace App\Services;

use App\DAO\AccountDAO;
use App\DAO\RideDAO;
use App\Exceptions\RequestRideNotAllowedException;
use Ramsey\Uuid\Uuid;

class RideService
{

    public function __construct(
        public RideDAO $rideDAO = new RideDAO(),
        public AccountDAO $accountDAO = new AccountDAO()
    ){}

    public function requestRide($inputRide)
    {
        $account = $this->accountDAO->getById($inputRide['passengerId']);
        if(!$account['is_passenger']){
            throw new RequestRideNotAllowedException('Account is not from a passenger');
        }
        $ride = $this->rideDAO->getActiveRidesByPassengerId($inputRide['passengerId']);
        if(count($ride)){
            throw new RequestRideNotAllowedException("Passenger is already in a ride");
        }
        $data = [
            'ride_id' => Uuid::uuid4()->toString(),
            'passenger_id' => $inputRide['passengerId'],
            'from_lat'=>$inputRide['from']['lat'],
            'from_long' => $inputRide['from']['long'],
            'to_lat'=>$inputRide['to']['lat'],
            'to_long' => $inputRide['to']['long'],
            'status' => 'requested',
        ];
        (new RideDAO())->save($data);
        $ride = (new RideDAO())->getRideById($data['ride_id']);
        return [
            'ride_id' =>$ride['ride_id']
        ];
    }

    public function getRide($ride_id)
    {
        return (new RideDAO())->getRideById($ride_id);
    }

}
