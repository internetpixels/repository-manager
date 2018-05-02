<?php

namespace InternetPixels\RepositoryManager\Entities;

/**
 * Class AbstractEntity
 * @package InternetPixels\RepositoryManager\Entities
 */
abstract class AbstractEntity implements EntityInterface
{

    /**
     * @var int
     */
    protected $id;

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

}