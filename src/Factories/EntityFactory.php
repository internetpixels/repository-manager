<?php

namespace InternetPixels\RepositoryManager\Factories;

use InternetPixels\RepositoryManager\Builder\QueryBuilder;
use InternetPixels\RepositoryManager\Entities\EntityInterface;

/**
 * Class EntityFactory
 * @package InternetPixels\RepositoryManager\Factories
 */
class EntityFactory
{

    /**
     * @var array
     */
    private $entities = [];

    /**
     * Register a new entity
     *
     * @param string $name
     * @param EntityInterface $entity
     * @throws \Exception
     */
    public function register(string $name, EntityInterface $entity)
    {
        if (isset($this->entities[$name])) {
            throw new \Exception('Entity already exists!');
        }

        $this->entities[$name] = $entity;
    }

    /**
     * Create a new entity
     *
     * @param $name
     * @return EntityInterface
     * @throws \Exception
     */
    public function create($name)
    {
        if ($this->exists($name) === true) {
            return clone $this->entities[$name];
        }

    }

    /**
     * Check if a given entity is registered.
     *
     * @param $entityName
     * @return bool
     * @throws \Exception
     */
    public function exists($entityName)
    {
        if (!isset($this->entities[$entityName])) {
            throw new \Exception('Entity is not registered!');
        }

        return true;
    }

    /**
     * Get a new query builder.
     *
     * @return QueryBuilder
     */
    public function createQueryBuilder()
    {
        return new QueryBuilder();
    }

}