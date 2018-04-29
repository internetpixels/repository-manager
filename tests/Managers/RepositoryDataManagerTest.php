<?php

namespace InternetPixels\RepositoryManager\Tests\Managers;

use InternetPixels\RepositoryManager\Managers\RepositoryDataManager;
use PHPUnit\Framework\TestCase;

/**
 * Class RepositoryDataManagerTest
 * @package InternetPixels\RepositoryManager\Tests\Managers
 */
class RepositoryDataManagerTest extends TestCase
{

    public function testQuery()
    {
        $mysqliMock = $this->getMockBuilder(\Mysqli::class)
            ->disableOriginalConstructor()
            ->setMethods(['query'])
            ->getMock();

        $mysqliMock->expects($this->once())
            ->method('query')
            ->willReturn(true);

        $manager = new RepositoryDataManager($mysqliMock);

        $this->assertTrue($manager->query('SELECT * FROM bla'));
    }

    public function testSanitize()
    {
        $mysqliMock = $this->getMockBuilder(\Mysqli::class)
            ->disableOriginalConstructor()
            ->setMethods(['real_escape_string'])
            ->getMock();

        $mysqliMock->expects($this->once())
            ->method('real_escape_string')
            ->willReturn('escaped string');

        $manager = new RepositoryDataManager($mysqliMock);

        $this->assertEquals('escaped string', $manager->sanitize('test string'));
    }

    public function testSanitize_WITH_integerAsOutput()
    {
        $mysqliMock = $this->getMockBuilder(\Mysqli::class)
            ->disableOriginalConstructor()
            ->setMethods(['real_escape_string'])
            ->getMock();

        $mysqliMock->expects($this->once())
            ->method('real_escape_string')
            ->willReturn('123456');

        $manager = new RepositoryDataManager($mysqliMock);
        $output = $manager->sanitize('123456', 'integer');

        $this->assertEquals(123456, $output);
        $this->assertTrue(is_int($output));
    }

}