<?php

namespace InternetPixels\RepositoryManager\Tests\Entities;

use InternetPixels\RepositoryManager\Entities\AbstractEntity;
use InternetPixels\RepositoryManager\Entities\EntityInterface;

/**
 * Class FakeEntity
 * @package InternetPixels\RepositoryManager\Tests\Entities
 */
class FakeEntity extends AbstractEntity implements EntityInterface
{

    /**
     * @var int
     */
    private $age;

    /**
     * @return int
     */
    public function getAge(): ?int
    {
        return $this->age;
    }

    /**
     * @param int $age
     */
    public function setAge(int $age)
    {
        $this->age = $age;
    }

}