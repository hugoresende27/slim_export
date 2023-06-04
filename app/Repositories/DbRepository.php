<?php

namespace Repositories;
use config\Db;
use PDO;
use PDOException;

class DbRepository
{
    private $db;
    private $conn;

    public function __construct()
    {
        $this->db = new Db();
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

    public function getCompanies(int $id = null): array
    {
        try {
            $sql = "SELECT * FROM companies";

            if ($id !== null) {
                $sql .= " WHERE id = :id";
            }

            return $this->executeQuery($sql, $id);
        } catch (PDOException $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function createNewCompany(array $data): int
    {
        try {
            $sql = "
            INSERT INTO companies (
                internal_id, other_id, company_social_name, company_comercial_name,
                email, nif, permit, linkedin, facebook, instagram, youtube, twitter,
                google, value_paid, observations, contract_start, contract_end
            ) VALUES (
                :internal_id, :other_id, :company_social_name, :company_comercial_name,
                :email, :nif, :permit, :linkedin, :facebook, :instagram, :youtube, :twitter,
                :google, :value_paid, :observations, :contract_start, :contract_end
            )
        ";

            $stmt = $this->conn->prepare($sql);

            $stmt->bindValue(':internal_id', $data['internal_id']);
            $stmt->bindValue(':other_id', $data['other_id']);
            $stmt->bindValue(':company_social_name', $data['company_social_name']);
            $stmt->bindValue(':company_comercial_name', $data['company_comercial_name']);
            $stmt->bindValue(':email', $data['email']);
            $stmt->bindValue(':nif', $data['nif']);
            $stmt->bindValue(':permit', $data['permit']);
            $stmt->bindValue(':linkedin', $data['linkedin']);
            $stmt->bindValue(':facebook', $data['facebook']);
            $stmt->bindValue(':instagram', $data['instagram']);
            $stmt->bindValue(':youtube', $data['youtube']);
            $stmt->bindValue(':twitter', $data['twitter']);
            $stmt->bindValue(':google', $data['google']);
            $stmt->bindValue(':value_paid', $data['value_paid']);
            $stmt->bindValue(':observations', $data['observations']);
            $stmt->bindValue(':contract_start', $data['contract_start']);
            $stmt->bindValue(':contract_end', $data['contract_end']);

            $stmt->execute();

            return $this->conn->lastInsertId();
        } catch (PDOException $e) {
            throw new \Exception($e->getMessage());
        }
    }


}
