<?php
namespace Http\Controllers;

use config\DbCrm;
use PDO;
use PDOException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Repositories\DbCrmRepository;

class PortaisController
{
    private DbCrmRepository $dbCrmRepository;

    public function __construct(DbCrmRepository $dbCrmRepository)
    {
        $this->dbCrmRepository = $dbCrmRepository;
    }

    public function index(Response $response): Response
    {
        $responseData = ['message' => 'Portais Controller index()'];

        return $this->createResponse($response, $responseData);
    }


    public function getPortais(Response $response, $id = null): Response
    {

        try {

            $portals = $this->dbCrmRepository->getPortals($id);
            $arrayPortals = [];
            foreach ($portals as $portal)
            {
                $arrayPortals[] = (array) $portal;
            }
            $arrayPortalsFinal = mb_convert_encoding($arrayPortals, 'UTF-8', 'UTF-8');
            return $this->createResponse($response, $arrayPortalsFinal);
        } catch (PDOException $e) {
            $error = array(
                'message' => $e->getMessage()
            );
            return $this->createResponse($response, $error);
        }
    }

    private function createResponse(Response $response, $data): Response
    {

        $response->getBody()->write(json_encode($data));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }


}
