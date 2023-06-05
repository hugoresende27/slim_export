<?php

namespace Services;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Connection\AMQPSSLConnection;
use PhpAmqpLib\Message\AMQPMessage;



class RabbitMQService
{
    public function publish($message)
    {
        $connection = new AMQPStreamConnection('slim_rabbitmq', '5672', 'guest', 'guest');

        $channel = $connection->channel();
        $channel->exchange_declare('test_exchange', 'direct', false, false, false);
        $channel->queue_declare('test_queue', false, false, false, false);
        $channel->queue_bind('test_queue', 'test_exchange', 'test_key');
        $msg = new AMQPMessage($message);
        $channel->basic_publish($msg, 'test_exchange', 'test_key');
        echo " [x] Sent $message to test_exchange / test_queue.\n";
        $channel->close();
        $connection->close();
    }
    public function consume()
    {

        $connection = new AMQPStreamConnection('slim_rabbitmq', '5672', 'guest', 'guest');

        $channel = $connection->channel();
        $callback = function ($msg) {
            echo ' [x] Received ', $msg->body, "\n";
        };
        $channel->queue_declare('test_queue', false, false, false, false);
        $channel->basic_consume('test_queue', '', false, true, false, false, $callback);
        echo 'Waiting for new message on test_queue', " \n";
        while ($channel->is_consuming()) {
            $channel->wait();
        }
        $channel->close();
        $connection->close();
    }
}
