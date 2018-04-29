<?php

namespace InternetPixels\RepositoryManager\Repositories;

use InternetPixels\RepositoryManager\Builder\QueryBuilder;
use InternetPixels\RepositoryManager\Entities\EntityInterface;
use InternetPixels\RepositoryManager\Factories\EntityFactory;
use InternetPixels\RepositoryManager\Managers\RepositoryDataManager;

/**
 * Class AbstractRepository
 * @package InternetPixels\RepositoryManager\Repositories
 */
abstract class AbstractRepository
{

    /**
     * @var RepositoryDataManager
     */
    private $dataManager;

    /**
     * @var EntityFactory
     */
    private $entityFactory;

    /**
     * @var string
     */
    protected $entityName;

    /**
     * @var QueryBuilder
     */
    protected $queryBuilder;

    /**
     * AbstractRepository constructor.
     *
     * @param RepositoryDataManager $dataManager
     * @param EntityFactory $entityFactory
     * @throws \Exception
     */
    public function __construct(RepositoryDataManager $dataManager, EntityFactory $entityFactory)
    {
        $this->dataManager = $dataManager;
        $this->entityFactory = $entityFactory;

        // Validate that the entity exists in our entity manger
        if ($this->entityFactory->exists($this->entityName) !== true) {
            throw new \Exception('Entity is not registered!');
        }

        if (!$this->queryBuilder instanceof QueryBuilder) {
            $this->queryBuilder = $this->entityFactory->createQueryBuilder();
        }
    }

    /**
     * Update a given entity in a table.
     *
     * @param EntityInterface $entity
     * @throws \Exception
     */
    public function create(EntityInterface $entity)
    {
        throw new \Exception('Please override this method in your own repository');
    }

    /**
     * Read from a table in a given entity.
     *
     * @param EntityInterface $entity
     * @return array
     */
    public function read(EntityInterface $entity)
    {
        $query = $this->queryBuilder->new($this->entityName)
            ->select()
            ->get();

        return $this->executeQueryForMultipleResults($query);
    }

    /**
     * Update a given entity in a table.
     *
     * @param EntityInterface $entity
     * @throws \Exception
     */
    public function update(EntityInterface $entity)
    {
        throw new \Exception('Please override this method in your own repository');
    }

    /**
     * Delete an entity given by an id.
     *
     * @param EntityInterface $entity
     * @return bool
     */
    public function delete(EntityInterface $entity)
    {
        $query = $this->queryBuilder->new()
            ->delete($this->entityName)
            ->where(['id' => $this->dataManager->sanitize($entity->getId(), 'integer')])
            ->limit(1)
            ->get();

        if ($this->dataManager->query($query) !== false) {
            return true;
        }

        return false;
    }

    /**
     * @param $query
     * @return array
     * @throws \Exception
     */
    protected function executeQueryForSingleResult($query)
    {
        $result = $this->dataManager->query($query);

        if ($result === false || $result->num_rows === 0) {
            throw new \Exception(sprintf('Record not found in %s', $this->entityName));
        }

        return $result->fetch_array();
    }

    /**
     * Execute query for multiple results
     *
     * @param $query
     * @return array
     * @throws \Exception
     */
    protected function executeQueryForMultipleResults($query)
    {
        $result = $this->dataManager->query($query);

        if ($result === false || $result->num_rows === 0) {
            throw new \Exception(sprintf('Record not found in %s', $this->entityName));
        }

        $items = [];

        while ($item = $result->fetch_assoc()) {
            $items[] = $item;
        }

        return $items;
    }

}