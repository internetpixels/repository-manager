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
    private static $entities = [];

    /**
     * Register a new entity
     *
     * @param string $name
     * @param EntityInterface $entity
     * @throws \Exception
     */
    public static function register(string $name, EntityInterface $entity)
    {
        if (isset(self::$entities[$name])) {
            throw new \Exception('Entity already exists!');
        }

        if (!$entity instanceof EntityInterface) {
            throw new \Exception('Entity is not implementing the EntityInterface!');
        }

        self::$entities[$name] = $entity;
    }

    /**
     * Create a new entity
     *
     * @param $name
     * @return EntityInterface
     * @throws \Exception
     */
    public static function create($name)
    {
        if (self::exists($name) === true) {
            return clone self::$entities[$name];
        }

    }

    /**
     * Check if a given entity is registered.
     *
     * @param $entityName
     * @return bool
     * @throws \Exception
     */
    public static function exists($entityName)
    {
        if (!isset(self::$entities[$entityName])) {
            throw new \Exception(sprintf('Entity (%s) is not registered!', $entityName));
        }

        return true;
    }

    /**
     * Get a new query builder.
     *
     * @return QueryBuilder
     */
    public static function createQueryBuilder()
    {
        return new QueryBuilder();
    }

}