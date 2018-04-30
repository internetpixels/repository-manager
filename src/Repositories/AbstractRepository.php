<?php

namespace InternetPixels\RepositoryManager\Repositories;

use InternetPixels\RepositoryManager\Builder\QueryBuilder;
use InternetPixels\RepositoryManager\Entities\AbstractEntity;
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
    protected $dataManager;

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
     * @throws \Exception
     */
    public function __construct(RepositoryDataManager $dataManager)
    {
        $this->dataManager = $dataManager;

        // Validate that the entity exists in our entity manger
        if (EntityFactory::exists($this->entityName) !== true) {
            throw new \Exception(sprintf('Entity (%s) is not registered!', $this->entityName));
        }

        if (!$this->queryBuilder instanceof QueryBuilder) {
            $this->queryBuilder = EntityFactory::createQueryBuilder();
        }
    }

    /**
     * Update a given entity in a table.
     *
     * @param AbstractEntity|EntityInterface $entity
     * @throws \Exception
     */
    public function create(AbstractEntity $entity)
    {
        throw new \Exception('Please override this method in your own repository');
    }

    /**
     * Read from a table.
     *
     * @return array
     */
    public function read()
    {
        $query = $this->queryBuilder->new($this->entityName)
            ->select()
            ->get();

        return $this->executeQueryForMultipleResults($query);
    }

    /**
     * Update a given entity in a table.
     *
     * @param AbstractEntity|EntityInterface $entity
     * @throws \Exception
     */
    public function update(AbstractEntity $entity)
    {
        throw new \Exception('Please override this method in your own repository');
    }

    /**
     * Delete an entity given by an id.
     *
     * @param AbstractEntity|EntityInterface $entity
     * @return bool
     */
    public function delete(AbstractEntity $entity)
    {
        $query = $this->queryBuilder->new($this->entityName)
            ->delete()
            ->where(['id' => $this->dataManager->sanitize($entity->getId(), 'integer')])
            ->limit(1)
            ->get();

        return $this->executeQuery($query);
    }

    /**
     * @param $query
     * @return bool
     * @throws \Exception
     */
    protected function executeQuery($query)
    {
        $result = $this->dataManager->query($query);

        if ($result === false) {
            throw new \Exception(sprintf('Query (%s) not executed for (%s)', $query, $this->entityName));
        }

        return true;
    }

    /**
     * @param $query
     * @return EntityInterface
     * @throws \Exception
     */
    protected function executeQueryForSingleResult($query)
    {
        $result = $this->dataManager->query($query);

        if ($result === false || $result->num_rows === 0) {
            throw new \Exception(sprintf('Record not found in %s', $this->entityName));
        }

        return $this->dataToEntity($result->fetch_array());
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
            throw new \Exception(sprintf('Record not found in %s with query "%s"', $this->entityName, $query));
        }

        $items = [];

        while ($item = $result->fetch_assoc()) {
            $items[] = $this->dataToEntity($item);
        }

        return $items;
    }

    /**
     * @param array $data
     * @return EntityInterface
     * @throws \Exception
     */
    protected function dataToEntity(array $data)
    {
        throw new \Exception('Please override this method in your own repository');
    }

}