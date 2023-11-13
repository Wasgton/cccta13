<?php

use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Infra\Controller\Api;

$app->get('/', function(Request $request, Response $response){
    $response->getBody()->write("Hello World");
    return $response->withStatus(201);
});

$app->group('/api', function (RouteCollectorProxy $route){
    $route->post('/signup', Api::class . ':signUp');
    $route->get('/account/{accountId}', Api::class . ':getAccount');

    $route->post('/request-ride', Api::class . ':requestRide');
    $route->get('/get-ride/{rideId}', Api::class . ':getRide');
});
