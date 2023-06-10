<?php

require __DIR__. '/../bootstrap/app.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use Repositories\DbMongoRepository;
use Repositories\DbRepository;


class receiveSQL
{
    private string $rabbitMqQueue;
    private DbRepository $DbRepository;
    private DbMongoRepository $dbMongoRepository;
    private string $collectionName = 'companies';


    public function __construct(string $rabbitMqQueue, DbRepository $DbRepository)
    {
        $this->rabbitMqQueue = $rabbitMqQueue;
        $this->DbRepository = $DbRepository;
        $this->dbMongoRepository = new DbMongoRepository($this->collectionName);;
    }

    public function startReceiving()
    {
        $connection = new AMQPStreamConnection('slim_rabbitmq', 5672, 'guest', 'guest');
        $channel = $connection->channel();

        $channel->queue_declare($this->rabbitMqQueue, false, false, false, false);

        echo " [*] Waiting for messages. SQL queries, queue: " . $this->rabbitMqQueue . "\n";

        $callback = function ($msg) {
            try {
                $this->processMessage($msg);
            } catch (Exception $e) {
                var_dump($e->getMessage() ?? 'nothing to declare');
            }
        };

        $channel->basic_consume($this->rabbitMqQueue, '', false, true, false, false, $callback);

        while ($channel->is_consuming()) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }

    private function processMessage($msg)
    {
        echo ' [x] Message: ', $msg->body, "\n";
        $arrayData = json_decode($msg->body, true);

//        echo ' [x] Message: ', $arrayData['action'].' --- '.$arrayData['data']['id'] ?? $arrayData['data']['internal_id'], "\n";

        if ($arrayData['action'] == 'create_company') {
            $this->DbRepository->createNewCompany($arrayData['data']);
        } else if ($arrayData['action'] == 'update_company') {
            $r = $this->DbRepository->updateCompany($arrayData['data']['id'] ?? $arrayData['data']['internal_id'], $arrayData['data']);
        } else if ($arrayData['action'] == 'create_company_mongo') {
            $this->dbMongoRepository->create($arrayData['data']);
        } else if ($arrayData['action'] == 'update_company_mongo') {
            $this->dbMongoRepository->update($arrayData['data']['id'] ?? $arrayData['data']['internal_id'],$arrayData['data']);
        }


    }
}

$rabbitMqQueue = 'sql';
$DbRepository = new DbRepository();


$receiver = new receiveSQL($rabbitMqQueue, $DbRepository);
$receiver->startReceiving();

