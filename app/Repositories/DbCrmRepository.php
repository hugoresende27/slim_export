<?php

namespace Repositories;
use config\DbCrm;
use PDO;
use PDOException;

class DbCrmRepository
{
    public function getPortals(int $id = null): array
    {
        try {
            $db = new DbCrm();
            $conn = $db->connect();

            if ($id !== null) {
                $sql = "SELECT * FROM portais WHERE id = :id";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
            } else {
                $sql = "SELECT * FROM portais";
                $stmt = $conn->query($sql);
            }

            $portals = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $db = null;

            return $portals;
        } catch (PDOException $e) {
            // Handle the exception, log the error, or throw a custom exception
            throw new \Exception($e->getMessage());
        }
    }
}
