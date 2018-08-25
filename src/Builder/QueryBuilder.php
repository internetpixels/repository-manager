<?php

namespace InternetPixels\RepositoryManager\Builder;

/**
 * Class QueryBuilder
 * @package InternetPixels\RepositoryManager\Builder
 */
class QueryBuilder
{

    /**
     * @var string
     */
    private $query;

    /**
     * @var string
     */
    private $tableName;

    /**
     * Build a new query
     *
     * @param $tableName
     * @return $this
     */
    public function new($tableName)
    {
        $this->query = '';
        $this->tableName = $tableName;

        return $this;
    }

    /**
     * @param array $fields
     * @return $this
     */
    public function select(array $fields = [])
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
     * @return $this
     */
    public function delete()
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
     * @return $this
     */
    public function insert(array $parameters)
    {
        $fields = array_keys($parameters);
        $values = array_values($parameters);

        foreach ($values as $key => $value) {
            if (is_string($value) || is_float($value)) {
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
     * Build the update query with given parameters.
     *
     * Important: Make sure that the values are sanitized before using this method!
     *
     * @param array $parameters
     * @return $this
     */
    public function update(array $parameters)
    {
        $update = [];

        foreach ($parameters as $field => $value) {
            if ($value === null) {
                $update[] = $field . ' = NULL';
            } elseif (is_string($value) || is_float($value)) {
                $update[] = $field . ' = "' . $value . '"';
            } else {
                $update[] = $field . ' = ' . $value;
            }
        }

        $this->query .= sprintf('UPDATE %s SET %s',
            $this->tableName,
            implode(', ', $update)
        );

        return $this;
    }

    /**
     * Add a where condition in a query, insert an array with a key => value pair. The key is the column name.
     *
     * Important: Make sure that the values are sanitized before using this method!
     *
     * @param array $parameters
     * @return $this
     */
    public function where(array $parameters)
    {
        $conditions = [];

        foreach ($parameters as $field => $value) {
            if (is_null($value)) {
                $conditions[] = $field . ' IS NULL';
            } elseif (is_int($value)) {
                $conditions[] = $field . ' = ' . $value;
            } else {
                $conditions[] = $field . ' = "' . $value . '"';
            }
        }

        if (count($conditions) >= 1) {
            $this->query .= ' WHERE ' . implode(' AND ', $conditions);
        }

        return $this;
    }

    /**
     * Add a an on condition in a query, insert an array with a key => value pair. The key is the column name.
     *
     * Important: Make sure that the values are sanitized before using this method!
     *
     * @param array $parameters
     * @return $this
     */
    public function on(array $parameters)
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
     * @return $this
     */
    public function join(string $tableName)
    {
        $this->query .= ' JOIN ' . $tableName;

        return $this;
    }

    /**
     * @param string $tableName
     * @return $this
     */
    public function leftJoin(string $tableName)
    {
        $this->query .= ' LEFT JOIN ' . $tableName;

        return $this;
    }

    /**
     * @param string $tableName
     * @return $this
     */
    public function rightJoin(string $tableName)
    {
        $this->query .= ' RIGHT JOIN ' . $tableName;

        return $this;
    }

    /**
     * Order a query by field name and direction.
     *
     * @param string $field
     * @param string $direction
     * @return $this
     */
    public function order(string $field, string $direction = 'DESC')
    {
        $direction = strtoupper($direction);

        $this->query .= sprintf(' ORDER BY %s %s', $field, $direction);

        return $this;
    }

    /**
     * Limit the amount of records
     *
     * @param int $limit
     * @return $this
     */
    public function limit(int $limit)
    {
        $this->query .= sprintf(' LIMIT %d', $limit);

        return $this;
    }

    /**
     * Get the query string
     *
     * @return string
     */
    public function get()
    {
        return $this->query;
    }

}