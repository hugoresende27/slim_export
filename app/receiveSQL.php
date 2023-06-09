<?php

require __DIR__. '/../bootstrap/app.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use Repositories\DbRepository;


class receiveSQL
{
    private string $rabbitMqQueue;
    private DbRepository $repository;


    public function __construct(string $rabbitMqQueue, DbRepository $repository)
    {
        $this->rabbitMqQueue = $rabbitMqQueue;
        $this->repository = $repository;
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

        if ($arrayData['action'] == 'create_company') {
            $this->repository->createNewCompany($arrayData['data']);
        } else if ($arrayData['action'] == 'update_company') {
            $this->repository->updateCompany($arrayData['data']['internal_id'], $arrayData['data']);
        }


    }
}

$rabbitMqQueue = 'sql';
$repository = new DbRepository();


$receiver = new receiveSQL($rabbitMqQueue, $repository);
$receiver->startReceiving();

