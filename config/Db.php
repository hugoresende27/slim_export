<?php

class Db
{

    private $host;
    private $user;
    private $pass;
    private $dbName;

    public function __construct() {
        $this->host = $_ENV['DB_HOST'];
        $this->user = $_ENV['DB_USER'];
        $this->pass = $_ENV['DB_PASS'];
        $this->dbName = $_ENV['DB_DATABASE'];
    }
    public function connect()
    {
        $conn_str = "mysql:host=$this->host;dbname=$this->dbName";
        $conn = new PDO($conn_str, $this->user, $this->pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $conn;
    }
}