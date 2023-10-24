<?php

namespace App;
use App\Services\AccountService;
use App\Services\RideService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Api
{
    public function signUp(Request $request, Response $response)
    {
        $inputs = $request->getParsedBody();
        $account = (new AccountService())->signUp($inputs);
        $response->getBody()->write(json_encode($account));
        $response->withStatus(201);
        return $response;
    }

    public function getAccount(Request $request, Response $response)
    {
        $accountId = $request->getAttribute('accountId');
        $account = (new AccountService())->getAccount($accountId);
        $response->getBody()->write(json_encode($account));
        $response->withStatus(200);
        return $response;
    }

    public function requestRide(Request $request, Response $response)
    {
        $inputs = $request->getParsedBody();
        $ride = (new RideService())->requestRide($inputs);
        $response->getBody()->write(json_encode($ride));
        $response->withStatus(201);
        return $response;
    }

    public function getRide(Request $request, Response $response)
    {
        $ride_id = $request->getAttribute('rideId');
        $ride = (new RideService())->getRide($ride_id);
        $response->getBody()->write(json_encode($ride));
        $response->withStatus(200);
        return $response;
    }

}
