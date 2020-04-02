<?php

namespace InternetPixels\RepositoryManager\Tests\Repositories;

use InternetPixels\RepositoryManager\Managers\RepositoryDataManager;
use InternetPixels\RepositoryManager\Repositories\AbstractRepository;
use PHPUnit\Framework\TestCase;

/**
 * Class RepositoryTest
 * @package InternetPixels\RepositoryManager\Tests\Repositories
 */
class RepositoryTest extends TestCase
{

    public function testFakeRepository_WITH_exceptionDisabled()
    {
        $dataManager = $this->getMockBuilder(RepositoryDataManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $repository = new FakeRepository($dataManager, false);
        $this->assertInstanceOf(AbstractRepository::class, $repository);
    }

}

/**
 * Class FakeRepository
 * @package InternetPixels\RepositoryManager\Tests\Repositories
 */
class FakeRepository extends AbstractRepository
{

    protected $entityName = 'unknown_name';

}