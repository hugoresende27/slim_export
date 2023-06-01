<?php

namespace config;
use PDO;

class DbCrm
{

    private $host;
    private $user;
    private $pass;
    private $dbName;

    public function __construct()
    {
        $this->host = $_ENV['DB_HOST_CRM'];
        $this->user = $_ENV['DB_USER_CRM'];
        $this->pass = $_ENV['DB_PASS_CRM'];
        $this->dbName = $_ENV['DB_DATABASE_CRM'];
    }

    public function connect(): PDO
    {
        $conn_str = "mysql:host=$this->host;dbname=$this->dbName";
        $conn = new PDO($conn_str, $this->user, $this->pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $conn;
    }
}