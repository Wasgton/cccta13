<?php

namespace App\Application\UseCases;

use App\Application\Exceptions\RequestRideNotAllowedException;
use App\Domain\Entities\Ride;
use App\Infra\Repository\AccountDAO;
use App\Infra\Repository\RideDAO;

class RequestRide
{

    public function __construct(
        public RideDAO $rideDAO = new RideDAO(),
        public AccountDAO $accountDAO = new AccountDAO()
    ){}

    public function execute($inputRide)
    {
        $account = $this->accountDAO->getById($inputRide['passengerId']);
        if(!is_null($account) && !$account->isPassenger){
            throw new RequestRideNotAllowedException('Account is not from a passenger');
        }
        $ride = $this->rideDAO->getActiveRidesByPassengerId($inputRide['passengerId']);
        if(count($ride)){
            throw new RequestRideNotAllowedException("Passenger is already in a ride");
        }
        $ride = Ride::create(
            $inputRide['passengerId'],
            $inputRide['from']['lat'],
            $inputRide['from']['long'],
            $inputRide['to']['lat'],
            $inputRide['to']['long']
        );
        (new RideDAO())->save($ride);
        return [
            'ride_id' => $ride->rideId
        ];
    }


}
