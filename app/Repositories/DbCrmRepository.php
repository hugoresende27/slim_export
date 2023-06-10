<?php

namespace Repositories;
use config\DbCrm;
use PDO;
use PDOException;

class DbCrmRepository
{
    private DbCrm $db;
    private PDO $conn;

    public function __construct()
    {
        $this->db = new DbCrm();
        $this->conn = $this->db->connect();
    }

    private function executeQuery(string $sql, int $id = null): array
    {
        $stmt = $this->conn->prepare($sql);

        if ($id !== null) {
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPortals(int $id = null): array
    {
        try {
            $sql = "SELECT * FROM portais";

            if ($id !== null) {
                $sql .= " WHERE id = :id";
            }

            return $this->executeQuery($sql, $id);
        } catch (PDOException $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function getCompanies(int $id = null): array
    {
        try {
            $sql = "SELECT * FROM empresa";

            if ($id !== null) {
                $sql .= " WHERE id = :id";
            }

            return $this->executeQuery($sql, $id);
        } catch (PDOException $e) {
            throw new \Exception($e->getMessage());
        }
    }



}
