<?php

namespace Http\Controllers;
use MongoDB\Client;
use Exception;
use MongoDB\Driver\ServerApi;
use config\Db;

use PDO;
use PDOException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class TestController
{

    public function index(Response $response)
    {


        $response->getBody()->write(json_encode($_ENV));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    public function userInfo(Response $response)
    {
                $userInfo = posix_getpwuid(posix_getuid());
        //        dd($userInfo);
        //        $directory = '/var/www/slim_app/text.txt';
        //        $file = fopen($directory, 'a+');
        //        fwrite($file, 'a');
        //        dd($file);
        $response->getBody()->write(json_encode($userInfo));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    public function rabbitMqConnection(Response $response)
    {

        $response->getBody()->write('rabbitMqConnection');

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    public function friendsDbTest(Request  $request, Response $response)
    {
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
    }

    public function mongoTest()
    {
        $uri = 'mongodb://atlas-sql-61b51f3eed4e692d36d8568a-afhxb.a.query.mongodb.net/aluraBD?ssl=true&authSource=admin';
        // Specify Stable API version 1
        $apiVersion = new ServerApi(ServerApi::V1);
        // Create a new client and connect to the server
        $client = new Client($uri, [], ['serverApi' => $apiVersion]);
        try {
            // Send a ping to confirm a successful connection
            $client->selectDatabase('admin')->command(['ping' => 1]);
            echo "Pinged your deployment. You successfully connected to MongoDB!\n";
        } catch (Exception $e) {
            printf($e->getMessage());
        }

    }
}