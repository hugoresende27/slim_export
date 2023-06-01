<?php

use DI\Container;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

require __DIR__ . '/../config/Db.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$container = new Container;
$settings =  require __DIR__ . '/../app/setting.php';
$settings($container);
AppFactory::setContainer($container);


$app = AppFactory::create();

$app->addErrorMiddleware(true,true,true);

$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write($_ENV['APP_NAME']);
    return $response;
});

$app->get('/test', function (Request $request, Response $response, $args) {



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

$app->add(new Tuupola\Middleware\HttpBasicAuthentication([
    "users" => [
        $_ENV['APP_USER'] =>  $_ENV['APP_PASS'],
        "dev" => "0000"
    ]
]));
$app->run();