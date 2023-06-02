<?php

namespace Http\Controllers;

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
}