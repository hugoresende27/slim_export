<?php
namespace Http\Controllers;

use config\DbCrm;
use PDO;
use PDOException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class PortaisController
{

    public function index(Request $request, Response $response)
    {
        $responseData = ['message' => 'Portais Controller index()'];

        $response->getBody()->write(json_encode($responseData));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }


    public function getPortais(Request $request, Response $response)
    {
        try {
            $portals = $this->getPortalsFromDatabase();

            $response->getBody()->write($portals);

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
        } catch (PDOException $e) {
            $error = array(
                'message' => $e->getMessage()
            );

            return $response
                ->withJson($error)
                ->withStatus(500);
        }
    }

    private function getPortalsFromDatabase()
    {
        $sql = "SELECT * FROM portais";

        $db = new DbCrm();
        $conn = $db->connect();

        $res = $conn->query($sql);
        $portals = $res->fetchAll(PDO::FETCH_OBJ);

        $db = null;
        return var_export($portals, true);
    }
}