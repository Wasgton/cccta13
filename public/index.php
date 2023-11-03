<?php

use App\Api;
use Slim\Factory\AppFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;

require_once __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();
$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

$app->group('/api', function (RouteCollectorProxy $route){
    $route->post('/signup', Api::class . ':signUp');
    $route->get('/account/{accountId}', Api::class . ':getAccount');

    $route->post('/request-ride', Api::class . ':requestRide');
    $route->get('/get-ride/{rideId}', Api::class . ':getRide');
});


$app->run();

