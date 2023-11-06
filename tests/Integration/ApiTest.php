<?php

namespace Tests\Integration;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Tests\TestCase;

class ApiTest extends TestCase
{
    /**
     * @throws GuzzleException
     */
    public function test_should_create_an_passenger_account()
    {
        $client = new Client([
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ]
        ]);
        $input = [
            'name' => $this->faker->firstName.' '.$this->faker->lastName,
            'email' => $this->faker->email,
            'cpf' => $this->faker->cpf(false),
            'isPassenger' => 1,
        ];

        $responseSignUp = $client->post('http://nginx/api/signup', ['body'=>json_encode($input)]);
        $responseSignUp = json_decode($responseSignUp->getBody(), true);
        $responseGetAccount = $client->get('http://nginx/api/account/' . $responseSignUp['accountId']);
        $responseGetAccount = json_decode($responseGetAccount->getBody(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertEquals($responseSignUp['account_id'], $responseGetAccount['account_id']);
        $this->assertEquals($input['name'], $responseGetAccount['name']);
        $this->assertEquals($input['email'], $responseGetAccount['email']);
        $this->assertEquals($input['cpf'], $responseGetAccount['cpf']);
    }

    public function test_should_request_a_ride()
    {
        $client = new Client([
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ]
        ]);
        $passengerInput = [
            'name' => $this->faker->firstName.' '.$this->faker->lastName,
            'email' => $this->faker->email,
            'cpf' => $this->faker->cpf(false),
            'isPassenger' => 1,
        ];
        $responseSignUp = $client->post('http://nginx/api/signup', ['body'=>json_encode($passengerInput)]);
        $responseSignUp = json_decode($responseSignUp->getBody(), true);
        $rideInput = [
            'passengerId' => $responseSignUp['account_id'],
            'from'=>[
                'lat'=> $this->faker->localCoordinates['latitude'],
                'long' => $this->faker->localCoordinates['longitude']
            ],
            'to'=>[
                'lat'=> $this->faker->localCoordinates['latitude'],
                'long' => $this->faker->localCoordinates['longitude']
            ]
        ];
        $responseRide = $client->post('http://nginx/api/request-ride', ['body'=>json_encode($rideInput)]);
        $responseRide = json_decode($responseRide->getBody(), true);
        $ride = $client->get('http://nginx/api/get-ride/' . $responseRide['ride_id']);
        $ride = json_decode($ride->getBody(), true);
        $this->assertEquals('requested', $ride->getStatus());
        $this->assertEquals($responseSignUp['account_id'], $ride->passengerId);
    }

}