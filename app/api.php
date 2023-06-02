<?php


use Http\Controllers\PortaisController;
use Http\Controllers\TestController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {

    //authentication
    $app->add(new Tuupola\Middleware\HttpBasicAuthentication([
        "users" => [
            $_ENV['APP_USER'] =>  $_ENV['APP_PASS'],
            "dev" => "0000"
        ]
    ]));

    //error log
    $app->addErrorMiddleware(true,true,true);


    //
    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write($_ENV['APP_NAME']);
        return $response;
    });


    //api routes
    $app->group('/api', function (Group $group) {


        //portals controller
        $group->get('/portals', [PortaisController::class, 'index']);
        $group->get('/portals/all', [PortaisController::class, 'getPortais']);
        $group->get('/portals/{id}', [PortaisController::class, 'getPortais']);


        //test controller
        $group->get('/test', [TestController::class, 'index']);
        $group->get('/test/friends', [TestController::class, 'friendsDbTest']);


    });


};
