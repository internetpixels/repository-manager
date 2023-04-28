<?php

namespace InternetPixels\RepositoryManager\Managers;

/**
 * Class RepositoryDataManager
 * @package InternetPixels\RepositoryManager\Managers
 */
class RepositoryDataManager
{
    /**
     * @var \Mysqli
     */
    private $mysqli;

    /**
     * RepositoryDataManager constructor.
     * @param \Mysqli $mysqli
     */
    public function __construct(\Mysqli $mysqli)
    {
        $this->mysqli = $mysqli;
    }

    /**
     * @param string $query
     * @return bool|\mysqli_result
     */
    public function query(string $query)
    {
        return $this->mysqli->query($query);
    }

    /**
     * Sanitize data before insert it in our data source.
     *
     * @param $input
     * @param string $outputType
     * @return mixed
     */
    public function sanitize($input, $outputType = 'string')
    {
        if ($input === null) {
            return $input;
        }

        $output = $this->mysqli->real_escape_string((string)$input);
        if ($outputType === 'string') {
            return (string)$output;
        }

        settype($output, $outputType);

        return $output;
    }

    /**
     * Get the last inserted id.
     *
     * @return mixed
     */
    public function lastInsertId()
    {
        return $this->mysqli->insert_id;
    }

}