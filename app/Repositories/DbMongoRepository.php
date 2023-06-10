<?php

namespace Repositories;
use config\DbMongo;
use MongoDB\Collection;
use MongoDB\BSON\ObjectID;

class DbMongoRepository
{
    private string $collectionName;
    private DbMongo $dbMongo;

    /**
     * @param string $collectionName
     */
    public function __construct(string $collectionName)
    {
        $this->dbMongo = new DbMongo();
        $this->collectionName = $collectionName;
    }

    /**
     * @return Collection
     */
    public function getCollection(): Collection
    {
        $database = $this->dbMongo->connect();
        return $database->selectCollection($this->collectionName);
    }

    /**
     * @param string $id
     * @return array|null
     */
    public function findById(string $id): ?array
    {
        $collection = $this->getCollection();
        $document = $collection->findOne(['_id' => new ObjectID($id)]);
        return $document ? (array)$document : null;
    }

    /**
     * @return array
     */
    public function findAll(): array
    {
        $collection = $this->getCollection();
        $documents = $collection->find()->toArray();
        return array_map(function ($document) {
            return (array)$document;
        }, $documents);
    }

    /**
     * @param array $data
     * @return array|null
     */
    public function create(array $data): ?array
    {
        $collection = $this->getCollection();
        $result = $collection->insertOne($data);
        if ($result->getInsertedCount() > 0) {
            $id = (string)$result->getInsertedId();
            return $this->findById($id);
        }
        return null;
    }

    /**
     * @param string $id
     * @param array $data
     * @return bool
     */
    public function update(string $id, array $data): bool
    {
        $collection = $this->getCollection();
        $result = $collection->updateOne(['_id' => new ObjectID($id)], ['$set' => $data]);
        return $result->getModifiedCount() > 0;
    }


    /**
     * @param string $id
     * @return bool
     */
    public function delete(string $id): bool
    {
        $collection = $this->getCollection();
        $result = $collection->deleteOne(['_id' => new ObjectID($id)]);
        return $result->getDeletedCount() > 0;
    }

    /**
     * @return bool
     */
    public function deleteAll(): bool
    {
        $collection = $this->getCollection();
        $result = $collection->deleteMany([]);
        return $result->getDeletedCount() > 0;
    }
}
