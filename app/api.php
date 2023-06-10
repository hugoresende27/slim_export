<?php


use Http\Controllers\CompaniesController;
use Http\Controllers\PortalsController;
use Http\Controllers\TestController;
use Http\Controllers\ToolsController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {

    //authentication
    $app->add(new Tuupola\Middleware\HttpBasicAuthentication([
        "users" => [
            $_ENV['APP_USER'] =>  $_ENV['APP_PASS'],
            "dev" => "0000"
        ]
    ]));

    //error log
    $app->addErrorMiddleware(true,true,true);


    //
    $app->get('/', function (Request $request, Response $response) {
        
        $response->getBody()->write($_ENV['APP_NAME']);
        return $response;
    });


    //api routes
    $app->group('/api', function (Group $group) {

        //companies controller
        //crm
        $group->get('/companies', [CompaniesController::class, 'index']);
        $group->get('/companies/crm', [CompaniesController::class, 'getCompaniesCrm']);
        $group->get('/companies/crm/{id}', [CompaniesController::class, 'getCompaniesCrm']);
        //db
        $group->get('/companies/all', [CompaniesController::class, 'getCompanies']);
        $group->get('/companies/{id}', [CompaniesController::class, 'getCompanies']);
        $group->post('/companies/create', [CompaniesController::class, 'create']);
        $group->put('/companies/{id}', [CompaniesController::class, 'update']);
        $group->delete('/companies/{id}', [CompaniesController::class, 'destroy']);
        $group->delete('/companies', [CompaniesController::class, 'destroyAll']);
        //using rabbitMQ
        $group->post('/companies-mq/create', [CompaniesController::class, 'createCompanyRabbitMQ']);
        $group->put('/companies-mq/{id}', [CompaniesController::class, 'updateCompanyRabbitMQ']);
        $group->get('/companies-mq/crm-copy', [CompaniesController::class, 'copyCompaniesCrm']);

        //portals controller
        $group->get('/portals', [PortalsController::class, 'index']);
        $group->get('/portals/all', [PortalsController::class, 'getPortals']);
        $group->get('/portals/{id}', [PortalsController::class, 'getPortals']);

        //test controller
        $group->get('/test', [TestController::class, 'index']);
        $group->get('/test/friends', [TestController::class, 'friendsDbTest']);
        $group->get('/test/user', [TestController::class, 'userInfo']);


        //json_excel convertor
        $group->post('/json-xlsx', [ToolsController::class, 'jsonToxlsx']);


        //rabbitMQ
        $group->get('/rabbitmq-connection', [RabbitMQController::class, 'testConnection']);
        $group->post('/rabbitmq-send/{message}', [RabbitMQController::class, 'sendMessage']);

        //mongoDB
        $group->get('/mongo-connection', [TestController::class, 'mongoConnectTest']);
        $group->get('/companies-mongo/all', [CompaniesController::class, 'getCompaniesMongo']);
        $group->post('/companies-mongo/create', [CompaniesController::class, 'createCompanyMongo']);
        $group->put('/companies-mongo/{id}', [CompaniesController::class, 'updateCompanyMongo']);
        $group->delete('/companies-mongo/{id}', [CompaniesController::class, 'destroyMongo']);
        $group->delete('/companies-mongo', [CompaniesController::class, 'destroyAllMongo']);

    });


};
