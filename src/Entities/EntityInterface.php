<?php

namespace InternetPixels\RepositoryManager\Entities;

/**
 * Interface EntityInterface
 * @package InternetPixels\RepositoryManager\Entities
 */
interface EntityInterface
{

    /**
     * Get the id.
     *
     * @return int
     */
    public function getId(): ?int;

    /**
     * Set the numeric id.
     *
     * @param int $id
     */
    public function setId(int $id);

}