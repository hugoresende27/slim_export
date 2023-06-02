<?php



use DI\Bridge\Slim\Bridge as SlimAppFactory;
use DI\Container;


require __DIR__ . '/../vendor/autoload.php';

require __DIR__ . '/../app/config/Db.php';
require __DIR__ . '/../app/config/DbCrm.php';
require __DIR__ . '/../app/Http/Controllers/PortaisController.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$container = new Container;
$settings =  require __DIR__ . '/../app/setting.php';
$settings($container);

$app = SlimAppFactory::create($container);

// routes
$routes = require __DIR__ . '/../app/api.php';
$routes($app);


return $app;

