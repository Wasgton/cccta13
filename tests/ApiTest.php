<?php

namespace Tests;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
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
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'cpf' => $this->faker->cpf(false),
            'isPassenger' => 1,
        ];

        $responseSignUp = $client->post('http://nginx/api/signup', ['body'=>json_encode($input)]);
        $responseSignUp = json_decode($responseSignUp->getBody(), true);

        $responseGetAccount = $client->get('http://nginx/api/account/' . $responseSignUp['account_id']);
        $responseGetAccount = json_decode($responseGetAccount->getBody(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertEquals($responseSignUp['account_id'], $responseGetAccount['account_id']);
        $this->assertEquals($input['name'], $responseGetAccount['name']);
        $this->assertEquals($input['email'], $responseGetAccount['email']);
        $this->assertEquals($input['cpf'], $responseGetAccount['cpf']);
    }


}