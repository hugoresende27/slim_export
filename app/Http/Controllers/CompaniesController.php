<?php
namespace Http\Controllers;

use PDOException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Repositories\DbCrmRepository;
use Repositories\DbRepository;
use Services\RabbitMQService;


class CompaniesController
{
    private DbCrmRepository $dbCrmRepository;
    private DbRepository $dbRepository;

    public function __construct(DbCrmRepository $dbCrmRepository, DbRepository $dbRepository)
    {
        $this->dbCrmRepository = $dbCrmRepository;
        $this->dbRepository = $dbRepository;
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


    public function createCompanyRabbitMQ(Request $request, Response $response): Response
    {
        // Get the JSON data from the request body
        $jsonData = $request->getBody()->getContents();
        // Convert the JSON string to an associative array
        $data = json_decode($jsonData, true);
        $i = 0;
        do {
            $rabbit = new \RabbitMQController();
            $rabbit->sendSQL($data);
            $i++;
        } while ($i < 1000);

        return $this->createResponse($response, $data, 201);

    }

    public function update(Request $request, Response $response, $id): Response
    {
        try {
            // Get the JSON data from the request body
            $jsonData = $request->getBody()->getContents();
            // Convert the JSON string to an associative array
            $data = json_decode($jsonData, true);

            $updated = $this->dbRepository->updateCompany($id, $data);

            if ($updated) {
                $responseData = ['message' => 'Company updated successfully'];
                return $this->createResponse($response, $responseData, 200);
            } else {
                $responseData = ['message' => 'Company not found'];
                return $this->createResponse($response, $responseData, 404);
            }
        } catch (PDOException $e) {
            $error = ['message' => $e->getMessage()];
            return $this->createResponse($response, $error, 500);
        }
    }


    public function destroy(Response $response, $id): Response
    {
        try {
            $deleted = $this->dbRepository->deleteCompany($id);
            if ($deleted) {
                $responseData = ['message' => 'Company deleted successfully'];
                return $this->createResponse($response, $responseData, 200);
            } else {
                $responseData = ['message' => 'Company not found'];
                return $this->createResponse($response, $responseData, 404);
            }
        } catch (PDOException $e) {
            $error = ['message' => $e->getMessage()];
            return $this->createResponse($response, $error, 500);
        }
    }

    public function destroyAll(Response $response): Response
    {
        try {
            $deleted = $this->dbRepository->deleteAllCompanies();
            if ($deleted) {
                $responseData = ['message' => 'All Companies deleted successfully'];
                return $this->createResponse($response, $responseData, 200);
            } else {
                $responseData = ['message' => 'Error deleting all'];
                return $this->createResponse($response, $responseData, 404);
            }
        } catch (PDOException $e) {
            $error = ['message' => $e->getMessage()];
            return $this->createResponse($response, $error, 500);
        }
    }


    ////* AUX PRIVATE FUNCTIONS *////
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
