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

            $stmt = $this->bindValuesSQL($sql, $data);

            $stmt->execute();

            return $this->conn->lastInsertId();
        } catch (PDOException $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function updateCompany(int $id, array $data): bool
    {
        try {
            $sql = "
            UPDATE companies SET
                internal_id = :internal_id,
                other_id = :other_id,
                company_social_name = :company_social_name,
                company_comercial_name = :company_comercial_name,
                email = :email,
                nif = :nif,
                permit = :permit,
                linkedin = :linkedin,
                facebook = :facebook,
                instagram = :instagram,
                youtube = :youtube,
                twitter = :twitter,
                google = :google,
                value_paid = :value_paid,
                observations = :observations,
                contract_start = :contract_start,
                contract_end = :contract_end
            WHERE id = :id
        ";

            $stmt = $this->bindValuesSQL($sql, $data);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);

            $stmt->execute();

            $rowCount = $stmt->rowCount();
            return $rowCount > 0;
        } catch (PDOException $e) {
            throw new \Exception($e->getMessage());
        }
    }




    /**
     * @param int $id
     * @return bool
     * @throws \Exception
     */
    public function deleteCompany(int $id): bool
    {
        $sql = "DELETE FROM companies WHERE id = :id";
        $params = [':id' => $id];
        return $this->executeDeleteQuery($sql, $params);
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function deleteAllCompanies(): bool
    {
        $sql = "DELETE FROM companies";
        return $this->executeDeleteQuery($sql);
    }
    /**
     * @param string $sql
     * @param array $data
     * @return false|\PDOStatement
     */
    public function bindValuesSQL(string $sql, array $data): \PDOStatement|false
    {
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
        return $stmt;
    }

    /**
     * @param string $sql
     * @param array $params
     * @return bool
     * @throws \Exception
     */
    private function executeDeleteQuery(string $sql, array $params = []): bool
    {
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            $rowCount = $stmt->rowCount();
            return $rowCount > 0;
        } catch (PDOException $e) {
            throw new \Exception($e->getMessage());
        }
    }


}
