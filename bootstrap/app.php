<?php

use DI\Bridge\Slim\Bridge as SlimAppFactory;
use DI\Container;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;


require __DIR__ . '/../vendor/autoload.php';

//config (Db connections)
require __DIR__ . '/../app/config/Db.php';
require __DIR__ . '/../app/config/DbCrm.php';
require __DIR__ . '/../app/config/DbMongo.php';

//helpers
require_once __DIR__ . '/../app/helpers.php';


//controllers
require __DIR__ . '/../app/Http/Controllers/PortalsController.php';
require __DIR__ . '/../app/Http/Controllers/TestController.php';
require __DIR__ . '/../app/Http/Controllers/CompaniesController.php';
require __DIR__ . '/../app/Http/Controllers/ToolsController.php';
require __DIR__ . '/../app/Http/Controllers/RabbitMQController.php';

//repositories
require __DIR__ . '/../app/Repositories/DbCrmRepository.php';
require __DIR__ . '/../app/Repositories/DbRepository.php';
require __DIR__ . '/../app/Repositories/DbMongoRepository.php';

//services


//commands

//mongo-db
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;

//for .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$container = new Container;

//settings
$settings =  require __DIR__ . '/../app/setting.php';
$settings($container);

$app = SlimAppFactory::create($container);


//// replace with mechanism to retrieve EntityManager in your app
//$cont = $app->getContainer();
//$entityManager = $cont->get(EntityManager::class);
//
//$commands = [
//    // If you want to add your own custom console commands,
//    // you can do so here.
//];
//
//ConsoleRunner::run(
//    new SingleManagerProvider($entityManager),
//    $commands
//);

//routes
$routes = require __DIR__ . '/../app/api.php';
$routes($app);


return $app;

