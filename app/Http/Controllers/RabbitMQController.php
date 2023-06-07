<?php

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class RabbitMQController
{
    private $rabbitMQHost;
    private $rabbitMQPort;
    private $rabbitMQUser;
    private $rabbitMQPassword;
    private $rabbitMQQueue;

    public function __construct()
    {
        // Initialize RabbitMQ connection settings
        $this->rabbitMQHost = $_ENV['MQ_HOST'];
        $this->rabbitMQPort = $_ENV['MQ_PORT'];
        $this->rabbitMQUser = $_ENV['MQ_USER'];
        $this->rabbitMQPassword = $_ENV['MQ_PASS'];
        $this->rabbitMQQueue = 'hello'; // Queue name for testing purposes
    }

    public function testConnection(Request $request, Response $response)
    {
        try {

            // Create a connection to RabbitMQ
            $connection = new AMQPStreamConnection($this->rabbitMQHost, $this->rabbitMQPort, $this->rabbitMQUser, $this->rabbitMQPassword);

            dd($connection);
            // Create a channel
            $channel = $connection->channel();

            // Declare the queue
            $channel->queue_declare($this->rabbitMQQueue, false, false, false, false);

            // Send a test message
            $message = new AMQPMessage('Hello, World!');
            $channel->basic_publish($message, '', $this->rabbitMQQueue);

            // Close the channel and connection
            $channel->close();
            $connection->close();

            // Return a success response
            $response->getBody()->write('RabbitMQ connection test successful');
            return $response->withStatus(200);
        } catch (Exception $e) {
            // Handle the exception and return an error response
            $response->getBody()->write('Error: ' . $e->getMessage());
            return $response->withStatus(500);
        }
    }
}

