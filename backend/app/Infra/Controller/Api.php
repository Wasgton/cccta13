<?php

namespace App\Infra\Controller;
use App\Application\UseCases\GetAccount;
use App\Application\UseCases\GetRide;
use App\Application\UseCases\RequestRide;
use App\Application\UseCases\SignUp;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Api
{
    public function signUp(Request $request, Response $response)
    {
        $inputs = $request->getParsedBody();
        $account = (new SignUp())->execute($inputs);
        $response->withHeader('Content-Type', 'application/json');
        $response->withStatus(201);
        $response->getBody()->write(json_encode($account));
        return $response;
    }

    public function getAccount(Request $request, Response $response)
    {
        $accountId = $request->getAttribute('accountId');
        $account = (new GetAccount())->execute($accountId);
        $response->getBody()->write(json_encode($account));
        $response->withStatus(200);
        return $response;
    }

    public function requestRide(Request $request, Response $response)
    {
        $inputs = $request->getParsedBody();
        $ride = (new RequestRide())->execute($inputs);
        $response->getBody()->write(json_encode($ride));
        $response->withStatus(201);
        return $response;
    }

    public function getRide(Request $request, Response $response)
    {
        $ride_id = $request->getAttribute('rideId');
        $ride = (new GetRide())->execute($ride_id);
        $ride = is_null($ride)?:$ride->toArray();
        $response->getBody()->write(json_encode($ride));
        $response->withStatus(200);
        return $response;
    }

}
