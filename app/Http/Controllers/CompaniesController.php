<?php
namespace Http\Controllers;

use PDOException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Repositories\DbCrmRepository;
use Repositories\DbRepository;

class CompaniesController
{
    private DbCrmRepository $dbCrmRepository;
    private DbRepository $dbRepository;

    public function __construct(DbCrmRepository $dbCrmRepository, DbRepository $dbRepository)
    {
        $this->dbCrmRepository = $dbCrmRepository;
        $this->dbRepository = $dbRepository;
    }

    public function create(Request $request, Response $response): Response
    {
        // Get the JSON data from the request body
        $jsonData = $request->getBody()->getContents();
        // Convert the JSON string to an associative array
        $data = json_decode($jsonData, true);
        // Create a new company record
        $company = $this->dbRepository->createNewCompany($data);
        // Return a success response with the created company data
        return $this->createResponse($response, $company, 201);

    }

    public function index(Response $response): Response
    {
        $responseData = ['message' => 'Companies Controller index()'];

        return $this->createResponse($response, $responseData, 200);
    }




    public function getCompaniesCrm(Response $response, $id = null): Response
    {

        try {
            $companies = $this->dbCrmRepository->getCompanies($id);
            return $this->createResponse($response,   $this->auxData($companies), 200);
        } catch (PDOException $e) {
            $error = [ 'message' => $e->getMessage()];
            return $this->createResponse($response, $error, 404);
        }
    }

    public function getCompanies(Response $response, $id = null): Response
    {

        try {

            $companies = $this->dbRepository->getCompanies($id);
            return $this->createResponse($response, $companies, 200);
        } catch (PDOException $e) {
            $error = [ 'message' => $e->getMessage()];
            return $this->createResponse($response, $error, 404);
        }
    }

    private function createResponse(Response $response, $data, $status): Response
    {

        $response->getBody()->write(json_encode($data));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }


    private function auxData(array $array): array|false|string|null
    {
        $arrayToReturn = [];
        foreach ($array as $item)
        {
            $arrayToReturn[] =  $item;
        }
        return mb_convert_encoding($arrayToReturn, 'UTF-8', 'UTF-8');
    }



}