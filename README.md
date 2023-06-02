# export_slim_api

### to run project : sudo docker-compose up -d --build
### to enter mysql : sudo docker exec -it slim_mysql mysql -u root -p
### to enter root project : sudo docker exec -it slim_php /bin/bash
### to stop and remove images : docker-compose down --rmi all

------------------------------------------------
### Docker set-up tut
-  https://dev.to/cherif_b/using-docker-for-slim-4-application-development-environment-1opm
------------------------------------------------
#### Authentication
- "tuupola/slim-basic-auth"
------------------------------------------------
#### Controllers :   "php-di/slim-bridge": "*"
- use DI\Bridge\Slim\Bridge as SlimAppFactory;
- $app = SlimAppFactory::create($container);
------------------------------------------------
#### dd function
- composer require symfony/var-dumper
- helpers.php and require helpers.php on bootstrap/app.php
------------------------------------------------
#### migrations
- https://github.com/cakephp/phinx
- ./vendor/bin/phinx init
- ./vendor/bin/phinx create MakeUsersTableMigration -c app/config/migrations.php


------------------------------------------------
#### TODO
- Get Companies
- use RabbitMQ 
- CRUD for properties
- export API