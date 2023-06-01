<?php

use config\Db;
use config\DbCrm;
use Controllers\PortaisController;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

$app = AppFactory::create();
$app->addErrorMiddleware(true,true,true);


$app->group('/api', function (Group $group) {
    $group->get('/', function (Request $request, Response $response, $args) {
        $response->getBody()->write($_ENV['APP_NAME']);
        return $response;
    });

    //get all friends
    $group->get('/friends/all', function (Request  $request, Response $response) {

        $sql = "SELECT * FROM friends";

        try {

            $db = new Db();
            $conn = $db->connect();
            $res =  $conn->query($sql);
            $friends = $res->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            $response->getBody()->write(json_encode($friends));
            return $response
                ->withHeader('content-type','application/json')
                ->withStatus(200);

        } catch (PDOException $e) {

            $error = array(
                'message' => $e->getMessage()
            );

            $response->getBody()->write(json_encode($error));
            return $response
                ->withHeader('content-type','application/json')
                ->withStatus(500);

        }
    });

//get all portals
//    $group->get('/portais', function (Request  $request, Response $response) {
//
//        $sql = "SELECT * FROM portais";
//
//        try {
//
//            $db = new DbCrm();
//            $conn = $db->connect();
//
//            $res =  $conn->query($sql);
//            $portals = $res->fetchAll(PDO::FETCH_OBJ);
//
//            $db = null;
//
//            $stringRepresentation = var_export($portals, true);
//
//            $response->getBody()->write($stringRepresentation);
//
//            return $response
//                ->withHeader('Content-Type', 'application/json')
//                ->withStatus(200);
//
//        } catch (PDOException $e) {
//
//            $error = array(
//                'message' => $e->getMessage()
//            );
//
//            $response->getBody()->write(json_encode($error));
//            return $response
//                ->withHeader('content-type','application/json')
//                ->withStatus(500);
//
//        }
//    });
    $group->get('/portais', [PortaisController::class, 'index']);

    $group->get('/test', function (Request $request, Response $response, $args) {

        $response->getBody()->write($_ENV['DB_DATABASE']);
        return $response;

    });
});

$app->add(new Tuupola\Middleware\HttpBasicAuthentication([
    "users" => [
        $_ENV['APP_USER'] =>  $_ENV['APP_PASS'],
        "dev" => "0000"
    ]
]));