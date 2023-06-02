<?php
namespace Http\Controllers;

use config\DbCrm;
use PDO;
use PDOException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class PortaisController
{

    public function index(Response $response): Response
    {
        $responseData = ['message' => 'Portais Controller index()'];

        return $this->createResponse($response, $responseData, 200);
    }


    public function getPortais(Response $response, $id = null): Response
    {

        try {
            $portals = $this->getPortalsFromDatabase($id);
            return $this->createResponse($response, $portals, 200);
        } catch (PDOException $e) {
            $error = array(
                'message' => $e->getMessage()
            );
            return $this->createResponse($response, $error, 200);
        }
    }

    private function createResponse(Response $response, $data, $statusCode): Response
    {
        $responseBody = json_encode($data);
        $response->getBody()->write($responseBody);
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($statusCode);
    }

    private function getPortalsFromDatabase(int $id = null): ?string
    {
        if ($id != null) {
            $sql = "SELECT * FROM portais WHERE id=".$id;
        } else {
            $sql = "SELECT * FROM portais";
        }


        $db = new DbCrm();
        $conn = $db->connect();

        $res = $conn->query($sql);
        $portals = $res->fetchAll(PDO::FETCH_OBJ);

        $db = null;
        return var_export($portals, true);
    }
}
