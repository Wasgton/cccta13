<?php

use Slim\Factory\AppFactory;

require_once __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();
$app->addBodyParsingMiddleware();
include_once __DIR__ . '/../app/Infra/Routes/Api.php';
$app->run();
