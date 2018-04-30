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

    public function testRegisterEntity()
    {
        $fakeEntity = new FakeEntity();

        EntityFactory::register('fake', $fakeEntity);

        /** @var FakeEntity $entity */
        $entity = EntityFactory::create('fake');
        $entity->setId(1);
        $entity->setAge(25);

        $this->assertInstanceOf(FakeEntity::class, $entity);
        $this->assertEquals(1, $entity->getId());
        $this->assertEquals(25, $entity->getAge());
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Entity (fake_new) is not registered!
     */
    public function testRegisterEntity_WITH_exception()
    {
        $this->assertInstanceOf(FakeEntity::class, EntityFactory::create('fake_new'));
    }

    /**
     * @expectedException \TypeError
     * @expectedExceptionMessage Argument 2 passed to InternetPixels\RepositoryManager\Factories\EntityFactory::register() must implement interface InternetPixels\RepositoryManager\Entities\EntityInterface
     */
    public function testRegisterEntity_WITH_exceptionEntityInvalid()
    {
        $fakeEntity = new \stdClass();

        EntityFactory::register('fake', $fakeEntity);
    }


}