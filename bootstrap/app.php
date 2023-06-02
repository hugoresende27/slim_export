<?php

use DI\Bridge\Slim\Bridge as SlimAppFactory;
use DI\Container;


require __DIR__ . '/../vendor/autoload.php';

//config (Db connections)
require __DIR__ . '/../app/config/Db.php';
require __DIR__ . '/../app/config/DbCrm.php';

//helpers
//require_once __DIR__ . '/../app/helpers.php';


//controllers
require __DIR__ . '/../app/Http/Controllers/PortaisController.php';
require __DIR__ . '/../app/Http/Controllers/TestController.php';
require __DIR__ . '/../app/Http/Controllers/CompaniesController.php';

//repositories
require __DIR__ . '/../app/Repositories/DbCrmRepository.php';

//for .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$container = new Container;

//settings
$settings =  require __DIR__ . '/../app/setting.php';
$settings($container);

$app = SlimAppFactory::create($container);

//routes
$routes = require __DIR__ . '/../app/api.php';
$routes($app);


return $app;

