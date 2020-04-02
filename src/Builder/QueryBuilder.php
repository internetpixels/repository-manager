<?php

namespace InternetPixels\RepositoryManager\Builder;

use InternetPixels\RepositoryManager\Managers\RepositoryDataManager;

/**
 * Class QueryBuilder
 * @package InternetPixels\RepositoryManager\Builder
 */
class QueryBuilder
{

    /**
     * @var string
     */
    private string $query;

    /**
     * @var string
     */
    private string $tableName;

    /**
     * @var RepositoryDataManager
     */
    private RepositoryDataManager $dataManager;

    /**
     * QueryBuilder constructor.
     * @param RepositoryDataManager $dataManager
     */
    public function __construct(RepositoryDataManager $dataManager)
    {
        $this->dataManager = $dataManager;
    }

    /**
     * Build a new query
     *
     * @param $tableName
     * @return QueryBuilder
     */
    public function new($tableName): QueryBuilder
    {
        $this->query = '';
        $this->tableName = $tableName;

        return $this;
    }

    /**
     * @param array $fields
     * @return QueryBuilder
     */
    public function select(array $fields = []): QueryBuilder
    {
        if (count($fields) === 0) {
            $this->query .= sprintf('SELECT * FROM %s', $this->tableName);

            return $this;
        }

        $this->query .= sprintf('SELECT %s FROM %s', implode(', ', $fields), $this->tableName);

        return $this;
    }

    /**
     * Delete a row in a given table.
     *
     * @return QueryBuilder
     */
    public function delete(): QueryBuilder
    {
        $this->query .= sprintf('DELETE FROM %s', $this->tableName);

        return $this;
    }

    /**
     * Insert a new row in a table.
     *
     * Important: Make sure that the values are sanitized before using this method!
     *
     * @param array $parameters
     * @return QueryBuilder
     */
    public function insert(array $parameters): QueryBuilder
    {
        $fields = array_keys($parameters);
        $values = array_values($parameters);

        foreach ($values as $key => $value) {
            if ($value === null) {
                $values[$key] = 'NULL';
            } elseif (is_string($value) || is_float($value)) {
                $values[$key] = '"' . $value . '"';
            }
        }

        $this->query .= sprintf('INSERT INTO %s (%s) VALUES (%s)',
            $this->tableName,
            implode(', ', $fields),
            implode(', ', $values)
        );

        return $this;
    }

    /**
     * Insert/update (replace) a row in a table.
     *
     * Important: Make sure that the values are sanitized before using this method!
     *
     * @param array $parameters
     * @return QueryBuilder
     */
    public function replaceInto(array $parameters): QueryBuilder
    {
        $fields = \array_keys($parameters);
        $values = \array_values($parameters);

        foreach ($values as $key => $value) {
            if ($value === null) {
                $values[$key] = 'NULL';
            } elseif (is_string($value) || is_float($value)) {
                $values[$key] = '"' . $value . '"';
            }
        }

        $this->query .= sprintf('REPLACE INTO %s (%s) VALUES (%s)',
            $this->tableName,
            \implode(', ', $fields),
            \implode(', ', $values)
        );

        return $this;
    }

    /**
     * Build the update query with given parameters.
     *
     * Important: Make sure that the values are sanitized before using this method!
     *
     * @param array $parameters
     * @param bool $sanitizeFields
     * @return QueryBuilder
     */
    public function update(array $parameters, bool $sanitizeFields = false): QueryBuilder
    {
        $update = [];

        foreach ($parameters as $field => $value) {
            if ($value === null) {
                $update[] = $field . ' = NULL';
            } elseif (\is_string($value) || \is_float($value)) {
                if ($sanitizeFields === true) {
                    $value = $this->dataManager->sanitize($value);
                }

                $update[] = $field . ' = "' . $value . '"';
            } else {
                if ($sanitizeFields === true) {
                    $value = $this->dataManager->sanitize($value);
                }

                $update[] = $field . ' = ' . $value;
            }
        }

        $this->query .= sprintf('UPDATE %s SET %s',
            $this->tableName,
            \implode(', ', $update)
        );

        return $this;
    }

    /**
     * Add a where condition in a query, insert an array with a key => value pair. The key is the column name.
     *
     * Important: Make sure that the values are sanitized before using this method!
     *
     * @param array $parameters
     * @return QueryBuilder
     */
    public function where(array $parameters): QueryBuilder
    {
        $conditions = [];

        foreach ($parameters as $field => $value) {
            if (\is_null($value)) {
                $conditions[] = $field . ' IS NULL';
            } elseif (\is_int($value)) {
                $conditions[] = $field . ' = ' . $value;
            } elseif (\is_array($value)) {
                $conditions[] = $field . ' IN(' . \implode(',', $value) . ')';
            } else {
                $conditions[] = $field . ' = "' . $value . '"';
            }
        }

        if (count($conditions) >= 1) {
            $this->query .= ' WHERE ' . \implode(' AND ', $conditions);
        }

        return $this;
    }

    /**
     * Add a an on condition in a query, insert an array with a key => value pair. The key is the column name.
     *
     * Important: Make sure that the values are sanitized before using this method!
     *
     * @param array $parameters
     * @return QueryBuilder
     */
    public function on(array $parameters): QueryBuilder
    {
        $conditions = [];

        foreach ($parameters as $field => $value) {
            if (is_null($value)) {
                $conditions[] = $field . ' IS NULL';
            } else {
                $conditions[] = $field . ' = ' . $value;
            }
        }

        if (count($conditions) >= 1) {
            $this->query .= ' ON ' . implode(' AND ', $conditions);
        }

        return $this;
    }

    /**
     * @param string $tableName
     * @return QueryBuilder
     */
    public function join(string $tableName): QueryBuilder
    {
        $this->query .= ' JOIN ' . $tableName;

        return $this;
    }

    /**
     * @param string $tableName
     * @return QueryBuilder
     */
    public function leftJoin(string $tableName): QueryBuilder
    {
        $this->query .= ' LEFT JOIN ' . $tableName;

        return $this;
    }

    /**
     * @param string $tableName
     * @return QueryBuilder
     */
    public function rightJoin(string $tableName): QueryBuilder
    {
        $this->query .= ' RIGHT JOIN ' . $tableName;

        return $this;
    }

    /**
     * Order a query by field name and direction.
     *
     * @param string $field
     * @param string $direction
     * @return QueryBuilder
     */
    public function order(string $field, string $direction = 'DESC'): QueryBuilder
    {
        $direction = strtoupper($direction);

        $this->query .= sprintf(' ORDER BY %s %s', $field, $direction);

        return $this;
    }

    /**
     * Limit the amount of records
     *
     * @param int $limit
     * @return QueryBuilder
     */
    public function limit(int $limit): QueryBuilder
    {
        $this->query .= sprintf(' LIMIT %d', $limit);

        return $this;
    }

    /**
     * Limit the amount of records with a given offset
     *
     * @param int $limit
     * @param int $offset
     * @return QueryBuilder
     */
    public function limitWithOffset(int $limit, int $offset): QueryBuilder
    {
        $this->query .= sprintf(' LIMIT %d,%d', $offset, $limit);

        return $this;
    }

    /**
     * Get the query string
     *
     * @return string
     */
    public function get(): string
    {
        return $this->query;
    }

}