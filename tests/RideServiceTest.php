<?php

use App\DAO\AccountDAO;
use App\DAO\RideDAO;
use App\Exceptions\RequestRideNotAllowedException;
use App\Services\AccountService;
use App\Services\RideService;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class RideServiceTest  extends TestCase
{

    public function test_should_request_a_ride()
    {
        $inputPassenger = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'cpf' => $this->faker->cpf(false),
            'isPassenger' => 1,
        ];
        $outputAccount = (new AccountService())->signUp($inputPassenger);
        $inputRide = [
            'passengerId' => $outputAccount['account_id'],
            'from'=>[
                'lat'=> number_format(-12.89957772925145,14),
                'long' => number_format(-38.31489672609199,14)
            ],
            'to'=>[
                'lat'=> number_format(-12.889956119375622,14),
                'long' => number_format(-38.34652533424635,14)
            ]
        ];
        $rideService = new RideService();
        $outputRide = $rideService->requestRide($inputRide);
        $outputGetRide = $rideService->getRide($outputRide['ride_id']);
        $this->assertEquals($outputGetRide['status'], 'requested');
        $this->assertEquals($outputAccount['account_id'], $outputGetRide['passenger_id']);
        $this->assertEquals($inputRide['from']['lat'], $outputGetRide['from_lat']);
        $this->assertEquals($inputRide['from']['long'], $outputGetRide['from_long']);
        $this->assertEquals($inputRide['to']['lat'], $outputGetRide['to_lat']);
        $this->assertEquals($inputRide['to']['long'], $outputGetRide['to_long']);
    }

    public function test_should_not_request_a_ride_when_passenger_is_not_passenger()
    {
        $this->expectException(RequestRideNotAllowedException::class);
        $this->expectExceptionMessage("Account is not from a passenger");
        $AccountDAOMock = $this->createMock(AccountDAO::class);
        $AccountDAOMock->method('getById')->willReturn([
            'isPassenger' => false
        ]);
        $inputRide = [
            'passengerId' => Uuid::uuid4()->toString(),
            'from'=>[
                'lat'=>-12.89957772925145,
                'long' => -38.31489672609199
            ],
            'to'=>[
                'lat'=>-12.889956119375622,
                'long' => -38.34652533424635
            ]
        ];
        (new RideService(accountDAO: $AccountDAOMock))->requestRide($inputRide);
    }

    public function test_shoud_not_request_a_ride_when_passenger_is_already_in_a_ride()
    {
        $AccountDAOMock = $this->createMock(AccountDAO::class);
        $AccountDAOMock->method('getById')->willReturn([
            'is_passenger' => true,
            'passengerId' => Uuid::uuid4()->toString(),
            'isDriver' => false
        ]);
        $rideDAOMock = $this->createMock(RideDAO::class);
        $rideDAOMock->method('getActiveRidesByPassengerId')->willReturn([
            'status' => 'accepted'
        ]);
        $inputRide = [
            'passengerId' => Uuid::uuid4()->toString(),
            'from'=>[
                'lat'=>-12.89957772925145,
                'long' => -38.31489672609199
            ],
            'to'=>[
                'lat'=>-12.889956119375622,
                'long' => -38.34652533424635
            ]
        ];
        $this->expectException(RequestRideNotAllowedException::class);
        $this->expectExceptionMessage("Passenger is already in a ride");
        (new RideService($rideDAOMock, $AccountDAOMock))->requestRide($inputRide);
    }
    
}
