<?php

namespace InternetPixels\RepositoryManager\Tests\Builder;

use InternetPixels\RepositoryManager\Builder\QueryBuilder;
use InternetPixels\RepositoryManager\Managers\RepositoryDataManager;
use PHPUnit\Framework\TestCase;

/**
 * Class QueryBuilderTest
 * @package InternetPixels\RepositoryManager\Tests\Builder
 */
class QueryBuilderTest extends TestCase
{

    public function testReturnTypeNew()
    {
        $query = new QueryBuilder($this->getMockDataManager());

        $this->assertInstanceOf(QueryBuilder::class, $query);
    }

    public function testReturnTypeUpdate()
    {
        $query = new QueryBuilder($this->getMockDataManager());

        $this->assertInstanceOf(QueryBuilder::class, $query->new('test')->update(['test' => 12,]));
    }

    public function testUpdateQuery_WITH_WhereCondition()
    {
        $builder = new QueryBuilder($this->getMockDataManager(true));

        $query = $builder->new('update_table')
            ->update([
                'name' => 'Test name',
                'age' => 25,
            ], true)
            ->where(['id' => 1])
            ->get();

        $expected = 'UPDATE update_table SET name = "sanitizedValue", age = sanitizedValue WHERE id = 1';

        $this->assertEquals($expected, $query);
    }

    public function testUpdateQuery_WITH_nullValue()
    {
        $builder = new QueryBuilder($this->getMockDataManager());

        $query = $builder->new('update_table')
            ->update([
                'name' => 'Test name',
                'age' => 25,
                'empty' => null,
            ])
            ->where(['id' => 1])
            ->get();

        $expected = 'UPDATE update_table SET name = "Test name", age = 25, empty = NULL WHERE id = 1';

        $this->assertEquals($expected, $query);
    }

    public function testUpdateQuery_WITH_WhereCondition_AND_limit()
    {
        $builder = new QueryBuilder($this->getMockDataManager());
        $query = $builder->new('update_table')
            ->update([
                'name' => 'Test name',
                'age' => 25,
            ])
            ->where(['id' => 1])
            ->limit(1)
            ->get();

        $expected = 'UPDATE update_table SET name = "Test name", age = 25 WHERE id = 1 LIMIT 1';

        $this->assertEquals($expected, $query);
    }

    public function testDeleteQuery_WITH_WhereCondition()
    {
        $builder = new QueryBuilder($this->getMockDataManager());
        $query = $builder->new('test_table')
            ->delete()
            ->where(['id' => 1])
            ->limit(1)
            ->get();

        $expected = 'DELETE FROM test_table WHERE id = 1 LIMIT 1';

        $this->assertEquals($expected, $query);
    }

    public function testDeleteQuery_WITH_WhereCondition_AND_nullValue()
    {
        $builder = new QueryBuilder($this->getMockDataManager());
        $query = $builder->new('test_table')
            ->delete()
            ->where([
                'id' => 1,
                'column' => null,
            ])
            ->limit(1)
            ->get();

        $expected = 'DELETE FROM test_table WHERE id = 1 AND column IS NULL LIMIT 1';

        $this->assertEquals($expected, $query);
    }

    public function testReplaceInsertQuery()
    {
        $builder = new QueryBuilder($this->getMockDataManager());
        $query = $builder->new('test_table_insert')
            ->replaceInto(['name' => 'Test name', 'age' => 25])
            ->get();

        $expected = 'REPLACE INTO test_table_insert (name, age) VALUES ("Test name", 25)';

        $this->assertEquals($expected, $query);
    }

    public function testInsertQuery()
    {
        $builder = new QueryBuilder($this->getMockDataManager());
        $query = $builder->new('test_table_insert')
            ->insert(['name' => 'Test name', 'age' => 25])
            ->get();

        $expected = 'INSERT INTO test_table_insert (name, age) VALUES ("Test name", 25)';

        $this->assertEquals($expected, $query);
    }

    public function testInsertQuery_WITH_nullValue()
    {
        $builder = new QueryBuilder($this->getMockDataManager());
        $query = $builder->new('test_table_insert')
            ->insert(['name' => 'Test name', 'age' => 25, 'test_column' => null])
            ->get();

        $expected = 'INSERT INTO test_table_insert (name, age, test_column) VALUES ("Test name", 25, NULL)';

        $this->assertEquals($expected, $query);
    }

    public function testReplaceIntoQuery()
    {
        $builder = new QueryBuilder($this->getMockDataManager());
        $query = $builder->new('test_table_insert')
            ->replaceInto(['name' => 'Test name', 'age' => 25])
            ->get();

        $expected = 'REPLACE INTO test_table_insert (name, age) VALUES ("Test name", 25)';

        $this->assertEquals($expected, $query);
    }

    public function testReplaceInto_WITH_nullValue()
    {
        $builder = new QueryBuilder($this->getMockDataManager());
        $query = $builder->new('test_table_insert')
            ->replaceInto(['name' => 'Test name', 'age' => 25, 'test_column' => null])
            ->get();

        $expected = 'REPLACE INTO test_table_insert (name, age, test_column) VALUES ("Test name", 25, NULL)';

        $this->assertEquals($expected, $query);
    }

    public function testSelectQuery()
    {
        $builder = new QueryBuilder($this->getMockDataManager());
        $query = $builder->new('test_table_select')
            ->select()
            ->get();

        $expected = 'SELECT * FROM test_table_select';

        $this->assertEquals($expected, $query);
    }

    public function testSelectQuery_WITH_whereIdCondition()
    {
        $builder = new QueryBuilder($this->getMockDataManager());
        $query = $builder->new('test_table_select')
            ->select()
            ->where(['id' => 1])
            ->get();

        $expected = 'SELECT * FROM test_table_select WHERE id = 1';

        $this->assertEquals($expected, $query);
    }

    public function testSelectQuery_WITH_whereIdInCondition()
    {
        $builder = new QueryBuilder($this->getMockDataManager());
        $query = $builder->new('test_table_select')
            ->select()
            ->where(['id' => [234, 5, 435, 23]])
            ->get();

        $expected = 'SELECT * FROM test_table_select WHERE id IN(234,5,435,23)';

        $this->assertEquals($expected, $query);
    }

    public function testSelectQuery_WITH_whereIdCondition_AND_customFields()
    {
        $builder = new QueryBuilder($this->getMockDataManager());
        $query = $builder->new('test_table_select')
            ->select(['id', 'name'])
            ->where(['id' => 1])
            ->get();

        $expected = 'SELECT id, name FROM test_table_select WHERE id = 1';

        $this->assertEquals($expected, $query);
    }

    public function testSelectQuery_WITH_whereIdCondition_AND_customFields_AND_orderByName()
    {
        $builder = new QueryBuilder($this->getMockDataManager());
        $query = $builder->new('test_table_select')
            ->select(['id', 'name'])
            ->where(['id' => 1])
            ->order('name')
            ->get();

        $expected = 'SELECT id, name FROM test_table_select WHERE id = 1 ORDER BY name DESC';

        $this->assertEquals($expected, $query);
    }

    public function testSelectQuery_WITH_whereIdCondition_AND_customFields_AND_orderByNameAsc()
    {
        $builder = new QueryBuilder($this->getMockDataManager());
        $query = $builder->new('test_table_select')
            ->select(['id', 'name'])
            ->where(['id' => 1])
            ->order('name', 'ASC')
            ->get();

        $expected = 'SELECT id, name FROM test_table_select WHERE id = 1 ORDER BY name ASC';

        $this->assertEquals($expected, $query);
    }

    public function testSelectQuery_WITH_jeftJoin()
    {
        $builder = new QueryBuilder($this->getMockDataManager());
        $query = $builder->new('test_table_select')
            ->select(['test_table_select.*', 'test_join.column'])
            ->leftJoin('test_join')
            ->on([
                'test_join.id' => 'test_table_select.test_join_id',
            ])
            ->get();

        $expected = 'SELECT test_table_select.*, test_join.column FROM test_table_select LEFT JOIN test_join ON test_join.id = test_table_select.test_join_id';

        $this->assertEquals($expected, $query);
    }

    public function testSelectQuery_WITH_jeftJoin_AND_onCondition()
    {
        $builder = new QueryBuilder($this->getMockDataManager());
        $query = $builder->new('test_table_select')
            ->select(['test_table_select.*', 'test_join.column'])
            ->leftJoin('test_join')
            ->on([
                'test_join.id' => 'test_table_select.test_join_id',
                'test_join.status' => 20,
            ])
            ->get();

        $expected = 'SELECT test_table_select.*, test_join.column FROM test_table_select LEFT JOIN test_join ON test_join.id = test_table_select.test_join_id AND test_join.status = 20';

        $this->assertEquals($expected, $query);
    }

    public function testSelectQuery_WITH_whereIdCondition_AND_limitWithOffset()
    {
        $builder = new QueryBuilder($this->getMockDataManager());
        $query = $builder->new('test_table_select')
            ->select()
            ->where(['id' => 1])
            ->limitWithOffset(5, 2)
            ->get();

        $expected = 'SELECT * FROM test_table_select WHERE id = 1 LIMIT 2,5';

        $this->assertEquals($expected, $query);
    }

    private function getMockDataManager(bool $withSanitizeMock = false)
    {
        if ($withSanitizeMock === true) {
            $dataManager = $this->getMockBuilder(RepositoryDataManager::class)
                ->disableOriginalConstructor()
                ->setMethods(['sanitize'])
                ->getMock();

            $dataManager->expects($this->atLeastOnce())
                ->method('sanitize')
                ->willReturn('sanitizedValue');

            return $dataManager;
        }

        return $this->getMockBuilder(RepositoryDataManager::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

}