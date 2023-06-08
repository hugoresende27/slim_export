<?php
namespace Http\Controllers;

use PDOException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use RabbitMQController;
use Repositories\DbCrmRepository;
use Repositories\DbRepository;


class CompaniesController
{
    private DbCrmRepository $dbCrmRepository;
    private DbRepository $dbRepository;
    private RabbitMQController $rabbitController;

    /**
     * @param DbCrmRepository $dbCrmRepository
     * @param DbRepository $dbRepository
     * @param RabbitMQController $rabbitController
     */
    public function __construct(DbCrmRepository $dbCrmRepository, DbRepository $dbRepository, RabbitMQController $rabbitController)
    {
        $this->dbCrmRepository = $dbCrmRepository;
        $this->dbRepository = $dbRepository;
        $this->rabbitController = $rabbitController;
    }


    /**
     * @param Response $response
     * @return Response
     */
    public function index(Response $response): Response
    {
        $responseData = ['message' => 'Companies Controller index()'];

        return $this->createResponse($response, $responseData, 200);
    }


    /**
     * @param Response $response
     * @param $id
     * @return Response
     * @throws \Exception
     */
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


    /**
     * @param Response $response
     * @param $id
     * @return Response
     * @throws \Exception
     */
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


    public function copyCompaniesCrm(Response $response): Response
    {

        $responseData = ['message' => 'Copy from crm'];

        $crmCompanies = $this->dbCrmRepository->getCompanies();
        foreach ($crmCompanies as $company)
        {

            if ($company['data_inicio_contrato'] == '0000-00-00' || $company['data_fim_contrato'] == '0000-00-00')
            {
                $date = '2023-01-01';
            } else {
                $date = $company['data_inicio_contrato'];
            }
            $arrayData = [
                'internal_id' => $company['id'],
                'other_id' => $company['id'], // Assuming the same ID is used for both internal and other ID
                'company_social_name' => utf8_encode($company['designacao_social']),
                'company_comercial_name' => utf8_encode($company['designacao_comercial']),
                'email' => '', // Add the appropriate email field from the $company array
                'nif' => $company['nif'],
                'permit' => $company['ami'], // Add the appropriate permit field from the $company array
                'linkedin' => utf8_encode($company['linkedin']),
                'facebook' => utf8_encode($company['facebook']),
                'instagram' => utf8_encode($company['instagram']),
                'youtube' => utf8_encode($company['youtube']),
                'twitter' => utf8_encode($company['twitter']),
                'google' => utf8_encode($company['google']),
                'value_paid' => 0.0, // Add the appropriate value paid field from the $company array
                'observations' =>utf8_encode( $company['observacoes']),
                'contract_start' => $date,
                'contract_end' => $date
            ];

            $this->rabbitController->sendSQL('create_company', $arrayData) ;
        }

        return $this->createResponse($response, $responseData, 200);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws \Exception
     */
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


    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function createCompanyRabbitMQ(Request $request, Response $response): Response
    {
        // Get the JSON data from the request body
        $jsonData = $request->getBody()->getContents();
        // Convert the JSON string to an associative array
        $data = json_decode($jsonData, true);

        var_dump($data);
        die();

        $this->rabbitController->sendSQL('create_company', $data);


        return $this->createResponse($response, 'rabbitMQ-create-company', 201);

    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $id
     * @return Response
     * @throws \Exception
     */
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

    /**
     * @param Request $request
     * @param Response $response
     * @param $id
     * @return Response
     */
    public function updateCompanyRabbitMQ(Request $request, Response $response, $id): Response
    {

        // Get the JSON data from the request body
        $jsonData = $request->getBody()->getContents();
        // Convert the JSON string to an associative array
        $data = json_decode($jsonData, true);
        $data['id'] = $id;

        $this->rabbitController->sendSQL('update_company', $data);

        return $this->createResponse($response, 'rabbitMQ-update-company '.$id, 201);

    }


    /**
     * @param Response $response
     * @param $id
     * @return Response
     * @throws \Exception
     */
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


    /**
     * @param Response $response
     * @return Response
     * @throws \Exception
     */
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


    ////* AUX PRIVATE FUNCTIONS *///////////////////////////

    /**
     * @param Response $response
     * @param $data
     * @param $status
     * @return Response
     */
    private function createResponse(Response $response, $data, $status): Response
    {

        $response->getBody()->write(json_encode($data));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }


    /**
     * @param array $array
     * @return array|false|string|null
     */
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
