<?php


use DI\Container;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

require __DIR__ . '/../app/config/Db.php';
require __DIR__ . '/../app/config/DbCrm.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$container = new Container;
$settings =  require __DIR__ . '/../app/setting.php';
$settings($container);
AppFactory::setContainer($container);

$app = AppFactory::create();

// routes
require __DIR__ . '/../app/api.php';





$app->run();