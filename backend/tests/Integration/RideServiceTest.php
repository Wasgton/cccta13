<?php

namespace Tests\Integration;

use App\Application\Exceptions\AcceptRideNotAllowed;
use App\Application\Exceptions\RequestRideNotAllowedException;
use App\Application\UseCases\AcceptRide;
use App\Application\UseCases\GetRide;
use App\Application\UseCases\RequestRide;
use App\Application\UseCases\SignUp;
use App\Domain\Entities\Account;
use App\Domain\Entities\Ride;
use App\Infra\Repository\AccountDAO;
use App\Infra\Repository\RideDAO;
use App\Services\AccountService;
use App\Services\RideService;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class RideServiceTest extends TestCase
{

    public function test_should_request_a_ride()
    {
        $inputPassenger = [
            'name'        => $this->faker->firstName . ' ' . $this->faker->lastName,
            'email'       => $this->faker->email,
            'cpf'         => $this->faker->cpf(false),
            'isPassenger' => 1,
        ];
        $account = (new SignUp())->execute($inputPassenger);
        $inputRide = [
            'passengerId' => $account->accountId,
            'from'        => [
                'lat'  => $this->faker->localCoordinates['latitude'],
                'long' => $this->faker->localCoordinates['longitude']
            ],
            'to'          => [
                'lat'  => $this->faker->localCoordinates['latitude'],
                'long' => $this->faker->localCoordinates['longitude']
            ]
        ];
        $outputRide = (new RequestRide())->execute($inputRide);
        $ride = (new GetRide())->execute($outputRide['ride_id']);
        $this->assertEquals($ride->getStatus(), 'requested');
        $this->assertEquals($account->accountId, $ride->passengerId);
        $this->assertEquals($inputRide['from']['lat'], $ride->fromLat);
        $this->assertEquals($inputRide['from']['long'], $ride->fromLong);
        $this->assertEquals($inputRide['to']['lat'], $ride->toLat);
        $this->assertEquals($inputRide['to']['long'], $ride->toLong);
    }

    public function test_should_not_request_a_ride_when_passenger_is_not_passenger()
    {
        $this->expectException(RequestRideNotAllowedException::class);
        $this->expectExceptionMessage("Account is not from a passenger");
        $AccountDAOMock = $this->createMock(AccountDAO::class);
        $AccountDAOMock->method('getById')->willReturn(
            Account::create(
                $this->faker->firstName . ' ' . $this->faker->lastName,
                $this->faker->email,
                $this->faker->cpf(false),
                'AAA1234',
                0,
                1
            )
        );
        $inputRide = [
            'passengerId' => Uuid::uuid4()->toString(),
            'from'        => [
                'lat'  => -12.89957772925145,
                'long' => -38.31489672609199
            ],
            'to'          => [
                'lat'  => -12.889956119375622,
                'long' => -38.34652533424635
            ]
        ];
        (new RequestRide(accountDAO: $AccountDAOMock))->execute($inputRide);
    }

    public function test_shoud_not_request_a_ride_when_passenger_is_already_in_a_ride()
    {
        $AccountDAOMock = $this->createMock(AccountDAO::class);
        $AccountDAOMock->method('getById')->willReturn(
            Account::create(
                $this->faker->firstName . ' ' . $this->faker->lastName,
                $this->faker->email,
                $this->faker->cpf(false),
                'AAA1234',
                1,
                0
            )
        );
        $rideDAOMock = $this->createMock(RideDAO::class);
        $rideDAOMock->method('getActiveRidesByPassengerId')->willReturn([
            'status' => 'accepted'
        ]);
        $inputRide = [
            'passengerId' => Uuid::uuid4()->toString(),
            'from'        => [
                'lat'  => -12.89957772925145,
                'long' => -38.31489672609199
            ],
            'to'          => [
                'lat'  => -12.889956119375622,
                'long' => -38.34652533424635
            ]
        ];
        $this->expectException(RequestRideNotAllowedException::class);
        $this->expectExceptionMessage("Passenger is already in a ride");
        (new RequestRide($rideDAOMock, $AccountDAOMock))->execute($inputRide);
    }

    public function test_should_accept_ride()
    {
        //Creating a passenger
        $inputPassenger = [
            'name'        => $this->faker->firstName . ' ' . $this->faker->lastName,
            'email'       => $this->faker->email,
            'cpf'         => $this->faker->cpf(false),
            'isPassenger' => 1,
        ];
        $signUp = new SignUp();
        $outputPassenger = $signUp->execute($inputPassenger);

        //Requesting a ride
        $inputRide = [
            'passengerId' => $outputPassenger->accountId,
            'from'        => [
                'lat'  => number_format($this->faker->localCoordinates['latitude'], 14),
                'long' => number_format($this->faker->localCoordinates['longitude'], 14)
            ],
            'to'          => [
                'lat'  => number_format($this->faker->localCoordinates['latitude'], 14),
                'long' => number_format($this->faker->localCoordinates['longitude'], 14)
            ]
        ];
        $outputRideRequest = (new RequestRide())->execute($inputRide);

        //Creating a driver
        $inputDriver = [
            'name'     => $this->faker->firstName . ' ' . $this->faker->lastName,
            'email'    => $this->faker->email,
            'cpf'      => $this->faker->cpf(false),
            'isDriver' => 1,
            'plate'    => 'ABC1234',
        ];
        $outputDriver = $signUp->execute($inputDriver);

        //Accepting a ride
        $data = [
            'ride_id'   => $outputRideRequest['ride_id'],
            'driver_id' => $outputDriver->accountId
        ];
        (new AcceptRide())->execute($data);
        $ride = (new GetRide())->execute($outputRideRequest['ride_id']);
        $this->assertEquals('accepted', $ride->getStatus());
        $this->assertEquals($outputDriver->accountId, $ride->getDriverId());
    }

    public function test_should_not_accept_a_ride_when_account_is_not_from_a_driver()
    {
        //Creating a passenger
        $inputPassenger = [
            'name'        => $this->faker->firstName . ' ' . $this->faker->lastName,
            'email'       => $this->faker->email,
            'cpf'         => $this->faker->cpf(false),
            'isPassenger' => 1,
        ];
        $signUp = new SignUp();
        $outputPassenger = $signUp->execute($inputPassenger);

        //Requesting a ride
        $inputRide = [
            'passengerId' => $outputPassenger->accountId,
            'from'        => [
                'lat'  => number_format($this->faker->localCoordinates['latitude'], 14),
                'long' => number_format($this->faker->localCoordinates['longitude'], 14)
            ],
            'to'          => [
                'lat'  => number_format($this->faker->localCoordinates['latitude'], 14),
                'long' => number_format($this->faker->localCoordinates['longitude'], 14)
            ]
        ];
        $requestRide = new RequestRide();
        $outputRideRequest = $requestRide->execute($inputRide);

        //Creating a driver
        $inputDriver = [
            'name'        => $this->faker->firstName . ' ' . $this->faker->lastName,
            'email'       => $this->faker->email,
            'cpf'         => $this->faker->cpf(false),
            'isPassenger' => 1,
        ];
        $outputDriver = $signUp->execute($inputDriver);

        //Accepting a ride
        $data = [
            'ride_id'   => $outputRideRequest['ride_id'],
            'driver_id' => $outputDriver->accountId
        ];
        $this->expectException(AcceptRideNotAllowed::class);
        $this->expectExceptionMessage("Account is not from a driver");
        (new AcceptRide())->execute($data);
    }

    public function test_should_not_accept_a_ride_when_ride_is_not_requested()
    {
        //Creating a passenger
        $inputPassenger = [
            'name'        => $this->faker->firstName . ' ' . $this->faker->lastName,
            'email'       => $this->faker->email,
            'cpf'         => $this->faker->cpf(false),
            'isPassenger' => 1,
        ];
        $signUp = new SignUp();
        $outputPassenger = $signUp->execute($inputPassenger);

        //Requesting a ride
        $inputRide = [
            'passengerId' => $outputPassenger->accountId,
            'from'        => [
                'lat'  => number_format($this->faker->localCoordinates['latitude'], 14),
                'long' => number_format($this->faker->localCoordinates['longitude'], 14)
            ],
            'to'          => [
                'lat'  => number_format($this->faker->localCoordinates['latitude'], 14),
                'long' => number_format($this->faker->localCoordinates['longitude'], 14)
            ]
        ];
        $outputRideRequest = (new RequestRide())->execute($inputRide);

        //Creating a driver
        $inputDriver = [
            'name'     => $this->faker->firstName . ' ' . $this->faker->lastName,
            'email'    => $this->faker->email,
            'cpf'      => $this->faker->cpf(false),
            'isDriver' => 1,
            'plate'    => 'ABC1234',
        ];
        $outputDriver = $signUp->execute($inputDriver);

        //Accepting a ride
        $ride = Ride::create(
            $outputPassenger->accountId,
            $outputRideRequest['from']['lat'],
            $outputRideRequest['from']['long'],
            $outputRideRequest['to']['lat'],
            $outputRideRequest['to']['long']
        );
        $rideDao = new RideDAO();
        $rideDao->save($ride);
        $ride->accept($outputDriver->accountId);

        $rideDaoMock = $this->createMock(RideDAO::class);
        $rideDaoMock->method('getRideById')->willReturn($ride);
        $this->expectException(AcceptRideNotAllowed::class);
        $this->expectExceptionMessage("Ride wasn't requested");
        $data = [
            'driver_id' => $outputDriver->accountId,
            'ride_id'   => $ride->rideId
        ];
        (new AcceptRide($rideDaoMock))->execute($data);
    }

    public function test_should_not_accept_a_ride_when_driver_is_already_in_a_ride()
    {
        $signUp = new SignUp();
        $requestRide = new RequestRide();
        $acceptRide = new AcceptRide();
        //Creating first  passenger
        $firstPassengerInput = [
            'name'        => $this->faker->firstName . ' ' . $this->faker->lastName,
            'email'       => $this->faker->email,
            'cpf'         => $this->faker->cpf(false),
            'isPassenger' => 1,
        ];
        $firstPassengerOutput = $signUp->execute($firstPassengerInput);
        //Creating a ride
        $firstRideInput = [
            'passengerId' => $firstPassengerOutput->accountId,
            'from'        => [
                'lat'  => number_format($this->faker->localCoordinates['latitude'], 14),
                'long' => number_format($this->faker->localCoordinates['longitude'], 14)
            ],
            'to'          => [
                'lat'  => number_format($this->faker->localCoordinates['latitude'], 14),
                'long' => number_format($this->faker->localCoordinates['longitude'], 14)
            ]
        ];
        $firstRide = (new RequestRide())->execute($firstRideInput);
        //Creating a driver
        $driverInput = [
            'name'     => $this->faker->firstName . ' ' . $this->faker->lastName,
            'email'    => $this->faker->email,
            'cpf'      => $this->faker->cpf(false),
            'isDriver' => 1,
            'plate'    => 'ABC1234',
        ];
        $driverOutput = $signUp->execute($driverInput);
        //Accepting the first ride
        $rideInput = [
            'ride_id'   => $firstRide['ride_id'],
            'driver_id' => $driverOutput->accountId
        ];
        $acceptRide->execute($rideInput);
        //Creating a second passenger
        $secondPassengerInput = [
            'name'        => $this->faker->firstName . ' ' . $this->faker->lastName,
            'email'       => $this->faker->email,
            'cpf'         => $this->faker->cpf(false),
            'isPassenger' => 1,
        ];
        $secondPassengerOuput = $signUp->execute($secondPassengerInput);
        //Creating a second ride
        $secondRideInput = [
            'passengerId' => $secondPassengerOuput->accountId,
            'from'        => [
                'lat'  => number_format($this->faker->localCoordinates['latitude'], 14),
                'long' => number_format($this->faker->localCoordinates['longitude'], 14)
            ],
            'to'          => [
                'lat'  => number_format($this->faker->localCoordinates['latitude'], 14),
                'long' => number_format($this->faker->localCoordinates['longitude'], 14)
            ]
        ];
        $secondRide = $requestRide->execute($secondRideInput);
        //Accepting the second ride
        $this->expectException(AcceptRideNotAllowed::class);
        $this->expectExceptionMessage('Driver is already in a ride');
        $acceptRide->execute([
            'ride_id'   => $secondRide['ride_id'],
            'driver_id' => $driverOutput->accountId
        ]);
    }

}
