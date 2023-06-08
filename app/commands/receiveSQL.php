<?php
require __DIR__. '/../../bootstrap/app.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use Repositories\DbRepository;

$rabbitMqQueue = 'sql';

$connection = new AMQPStreamConnection('slim_rabbitmq', 5672, 'guest', 'guest');
$channel = $connection->channel();

$channel->queue_declare($rabbitMqQueue, false, false, false, false);

echo " [*] Waiting for messages. Sql querys , queue ".$rabbitMqQueue."\n";

$callback = function ($msg) {
    echo ' [x] data : ', $msg->body, "\n";
    $repository = new DbRepository();
    $arrayData = json_decode( $msg->body, true) ;
    $repository->createNewCompany($arrayData);

};

$channel->basic_consume($rabbitMqQueue, '', false, true, false, false, $callback);

while ($channel->is_consuming()) {
    $channel->wait();
}

$channel->close();
$connection->close();
?>