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
    private string $rabbitMQQueueTests;
    private string $rabbitMQQueueSQL;

    public function __construct()
    {
        // Initialize RabbitMQ connection settings
        $this->rabbitMQHost = $_ENV['MQ_HOST'];
        $this->rabbitMQPort = $_ENV['MQ_PORT'];
        $this->rabbitMQUser = $_ENV['MQ_USER'];
        $this->rabbitMQPassword = $_ENV['MQ_PASS'];
        $this->rabbitMQQueueTests = 'tests'; // Queue name for testing purposes
        $this->rabbitMQQueueSQL = 'sql'; // Queue name for sql purposes
    }


    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function testConnection(Request $request, Response $response): Response
    {
        try {

            // Create a connection to RabbitMQ
            $connection = new AMQPStreamConnection($this->rabbitMQHost, $this->rabbitMQPort, $this->rabbitMQUser, $this->rabbitMQPassword);


            // Create a channel
            $channel = $connection->channel();

            // Declare the queue
            $channel->queue_declare($this->rabbitMQQueueTests, false, false, false, false);

            // Send a test message
            for ($i = 0 ; $i < 10 ; $i++)
            {
                $message = new AMQPMessage('--'.$i.'--'.$_ENV['APP_NAME']);
                $channel->basic_publish($message, '', $this->rabbitMQQueueTests);
            }


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


    /**
     * @param $message
     * @param Response $response
     * @return Response
     * @throws Exception
     */
    public function sendMessage($message, Response $response): Response
    {

        $currentTime = new DateTime();
        $currentTime->add(new DateInterval('PT1H'));//add one hour
        $currentTime = $currentTime->format('Y-m-d H:i:s');


        $message = $message.$currentTime;
        $connection = new AMQPStreamConnection($this->rabbitMQHost, $this->rabbitMQPort, $this->rabbitMQUser, $this->rabbitMQPassword);
        $channel = $connection->channel();

        $channel->queue_declare($this->rabbitMQQueueTests, false, false, false, false);

        $msg = new AMQPMessage($message);
        $channel->basic_publish($msg, '', $this->rabbitMQQueueTests);


        $channel->close();
        $connection->close();
        // Return a success response
        $response->getBody()->write('message send with success :: '.$message);
        return $response->withStatus(200);
    }


    public function sendSQL(string $action, $data = null): bool
    {
        try {
            $connection = new AMQPStreamConnection($this->rabbitMQHost, $this->rabbitMQPort, $this->rabbitMQUser, $this->rabbitMQPassword);
            $channel = $connection->channel();

            $channel->queue_declare($this->rabbitMQQueueSQL, false, false, false, false);
            $message = [
                'action' => $action,
                'data' => $data,
            ];

            $msg = new AMQPMessage(json_encode($message));
            $channel->basic_publish($msg, '', $this->rabbitMQQueueSQL);

            $channel->close();
            $connection->close();

            return true; // Operation succeeded
        } catch (Exception $e) {
            // Handle any exceptions or errors that occurred during the operation
            // Log the error or perform any necessary error handling
            dd($e);
            return false; // Operation failed
        }
    }





}

