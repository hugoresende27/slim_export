<?php
namespace config;
use MongoDB\Client;
use MongoDB\Database;
use MongoDB\Driver\Exception\Exception;

class DbMongo
{
    private $uri;
    private $dbName;

    public function __construct()
    {
        $this->uri = $_ENV['MONGO_DB_URI'];
        $this->dbName = $_ENV['MONGO_DB_NAME'];
    }

    public function connect(): Database
    {
        $client = new Client($this->uri);
        try {
            // Access the desired database
            return $client->selectDatabase($this->dbName);
        } catch (Exception $e) {
           printf($e->getMessage());
        }
    }
}
