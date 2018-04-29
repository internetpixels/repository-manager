<?php

namespace InternetPixels\RepositoryManager\Tests\Repositories;

use InternetPixels\RepositoryManager\Factories\EntityFactory;
use InternetPixels\RepositoryManager\Tests\Entities\FakeEntity;
use PHPUnit\Framework\TestCase;

/**
 * Class RepositoryManagerTest
 * @package InternetPixels\RepositoryManager\Tests\Repositories
 */
class RepositoryManagerTest extends TestCase
{

    /**
     * @var EntityFactory
     */
    private $entityFactory;

    public function setUp()
    {
        $this->entityFactory = new EntityFactory();
    }

    public function testRegisterEntity()
    {
        $fakeEntity = new FakeEntity();

        $this->entityFactory->register('fake', $fakeEntity);

        /** @var FakeEntity $entity */
        $entity = $this->entityFactory->create('fake');
        $entity->setId(1);
        $entity->setAge(25);

        $this->assertInstanceOf(FakeEntity::class, $entity);
        $this->assertEquals(1, $entity->getId());
        $this->assertEquals(25, $entity->getAge());
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Entity is not registered!
     */
    public function testRegisterEntity_WITH_exception()
    {
        $this->assertInstanceOf(FakeEntity::class, $this->entityFactory->create('fake'));
    }

    /**
     * @expectedException \TypeError
     * @expectedExceptionMessage Argument 2 passed to InternetPixels\RepositoryManager\Factories\EntityFactory::register() must implement interface InternetPixels\RepositoryManager\Entities\EntityInterface
     */
    public function testRegisterEntity_WITH_exceptionEntityInvalid()
    {
        $fakeEntity = new \stdClass();

        $this->entityFactory->register('fake', $fakeEntity);
    }


}