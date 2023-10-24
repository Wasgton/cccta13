<?php

use App\DAO\AccountDAO;
use App\DAO\RideDAO;
use App\Exceptions\AcceptRideNotAllowed;
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
                'lat'=> $this->faker->localCoordinates['latitude'],
                'long' => $this->faker->localCoordinates['longitude']
            ],
            'to'=>[
                'lat'=> $this->faker->localCoordinates['latitude'],
                'long' => $this->faker->localCoordinates['longitude']
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

    public function test_should_accept_ride()
    {
        //Creating a passenger
        $inputPassenger = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'cpf' => $this->faker->cpf(false),
            'isPassenger' => 1,
        ];
        $outputPassenger = (new AccountService())->signUp($inputPassenger);

        //Requesting a ride
        $inputRide = [
            'passengerId' => $outputPassenger['account_id'],
            'from'=>[
                'lat'=> number_format($this->faker->localCoordinates['latitude'],14),
                'long' => number_format($this->faker->localCoordinates['longitude'],14)
            ],
            'to'=>[
                'lat'=> number_format($this->faker->localCoordinates['latitude'],14),
                'long' => number_format($this->faker->localCoordinates['longitude'],14)
            ]
        ];
        $rideService = new RideService();
        $outputRideRequest = $rideService->requestRide($inputRide);

        //Creating a driver
        $inputDriver = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'cpf' => $this->faker->cpf(false),
            'isDriver' => 1,
            'plate' => 'ABC1234',
        ];
        $outputDriver = (new AccountService())->signUp($inputDriver);

        //Accepting a ride
        $data = [
            'ride_id' => $outputRideRequest['ride_id'],
            'driver_id' => $outputDriver['account_id']
        ];
        $rideService->acceptRide($data);
        $ride = $rideService->getRide($outputRideRequest['ride_id']);
        $this->assertEquals('accepted', $ride['status']);
        $this->assertEquals( $outputDriver['account_id'], $ride['driver_id']);
    }

    public function test_should_not_accept_a_ride_when_account_is_not_from_a_driver()
    {
        //Creating a passenger
        $inputPassenger = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'cpf' => $this->faker->cpf(false),
            'isPassenger' => 1,
        ];
        $outputPassenger = (new AccountService())->signUp($inputPassenger);

        //Requesting a ride
        $inputRide = [
            'passengerId' => $outputPassenger['account_id'],
            'from'=>[
                'lat'=> number_format($this->faker->localCoordinates['latitude'],14),
                'long' => number_format($this->faker->localCoordinates['longitude'],14)
            ],
            'to'=>[
                'lat'=> number_format($this->faker->localCoordinates['latitude'],14),
                'long' => number_format($this->faker->localCoordinates['longitude'],14)
            ]
        ];
        $rideService = new RideService();
        $outputRideRequest = $rideService->requestRide($inputRide);

        //Creating a driver
        $inputDriver = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'cpf' => $this->faker->cpf(false),
            'isPassenger' => 1,
        ];
        $outputDriver = (new AccountService())->signUp($inputDriver);

        //Accepting a ride
        $data = [
            'ride_id' => $outputRideRequest['ride_id'],
            'driver_id' => $outputDriver['account_id']
        ];
        $this->expectException(AcceptRideNotAllowed::class);
        $this->expectExceptionMessage("Account is not from a driver");
        $rideService->acceptRide($data);
    }

    public function test_should_not_accept_a_ride_when_ride_is_not_requested()
    {
        //Creating a passenger
        $inputPassenger = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'cpf' => $this->faker->cpf(false),
            'isPassenger' => 1,
        ];
        $outputPassenger = (new AccountService())->signUp($inputPassenger);

        //Requesting a ride
        $inputRide = [
            'passengerId' => $outputPassenger['account_id'],
            'from'=>[
                'lat'=> number_format($this->faker->localCoordinates['latitude'],14),
                'long' => number_format($this->faker->localCoordinates['longitude'],14)
            ],
            'to'=>[
                'lat'=> number_format($this->faker->localCoordinates['latitude'],14),
                'long' => number_format($this->faker->localCoordinates['longitude'],14)
            ]
        ];
        $rideService = new RideService();
        $outputRideRequest = $rideService->requestRide($inputRide);

        //Creating a driver
        $inputDriver = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'cpf' => $this->faker->cpf(false),
            'isDriver' => 1,
            'plate' => 'ABC1234',
        ];
        $outputDriver = (new AccountService())->signUp($inputDriver);

        //Accepting a ride
        $rideDaoMock = $this->createStub(RideDAO::class);
        $rideDaoMock->method('getRideById')->willReturn(['status'=>'accepted']);
        $this->expectException(AcceptRideNotAllowed::class);
        $this->expectExceptionMessage("Ride wasn't requested");
        $data = [
            'driver_id' => $outputDriver['account_id']
        ];
        (new RideService($rideDaoMock))->acceptRide($data);
    }

    public function test_should_not_accept_a_ride_when_driver_is_already_in_a_ride()
    {
        $accountService = new AccountService();
        $rideService = new RideService();
        //Creating first  passenger
        $firstPassengerInput =  [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'cpf' => $this->faker->cpf(false),
            'isPassenger' => 1,
        ];
        $firstPassengerOutput = $accountService->signUp($firstPassengerInput);
        //Creating a ride
        $firstRideInput = [
            'passengerId' => $firstPassengerOutput['account_id'],
            'from'=>[
                'lat'=> number_format($this->faker->localCoordinates['latitude'],14),
                'long' => number_format($this->faker->localCoordinates['longitude'],14)
            ],
            'to'=>[
                'lat'=> number_format($this->faker->localCoordinates['latitude'],14),
                'long' => number_format($this->faker->localCoordinates['longitude'],14)
            ]
        ];
        $firstRide = $rideService->requestRide($firstRideInput);
        //Creating a driver
        $driverInput = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'cpf' => $this->faker->cpf(false),
            'isDriver' => 1,
            'plate' => 'ABC1234',
        ];
        $driverOutput = $accountService->signUp($driverInput);
        //Accepting the first ride
        $rideInput = [
            'ride_id' => $firstRide['ride_id'],
            'driver_id' => $driverOutput['account_id']
        ];
        $rideService->acceptRide($rideInput);
        //Creating a second passenger
        $secondPassengerInput = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'cpf' => $this->faker->cpf(false),
            'isPassenger' => 1,
        ];
        $secondPassengerOuput = $accountService->signUp($secondPassengerInput);
        //Creating a second ride
        $secondRideInput = [
            'passengerId' => $secondPassengerOuput['account_id'],
            'from'=>[
                'lat'=> number_format($this->faker->localCoordinates['latitude'],14),
                'long' => number_format($this->faker->localCoordinates['longitude'],14)
            ],
            'to'=>[
                'lat'=> number_format($this->faker->localCoordinates['latitude'],14),
                'long' => number_format($this->faker->localCoordinates['longitude'],14)
            ]
        ];
        $secondRide = $rideService->requestRide($secondRideInput);
        //Accepting the second ride
        $this->expectException(AcceptRideNotAllowed::class);
        $this->expectExceptionMessage('Driver is already in a ride');
        $rideService->acceptRide([
            'ride_id' => $secondRide['ride_id'],
            'driver_id' => $driverOutput['account_id']
        ]);
    }
    
}
