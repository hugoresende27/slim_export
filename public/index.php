<?php

use DI\Container;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';


$container = new Container;
$settings =  require __DIR__ . '/../app/setting.php';
$settings($container);
AppFactory::setContainer($container);


$app = AppFactory::create();

$app->addErrorMiddleware(true,true,true);

$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Hello export_slim and docker env!");
    return $response;
});

$app->run();