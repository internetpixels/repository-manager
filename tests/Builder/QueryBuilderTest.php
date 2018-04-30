<?php

namespace InternetPixels\RepositoryManager\Tests\Builder;

use InternetPixels\RepositoryManager\Builder\QueryBuilder;
use PHPUnit\Framework\TestCase;

/**
 * Class QueryBuilderTest
 * @package InternetPixels\RepositoryManager\Tests\Builder
 */
class QueryBuilderTest extends TestCase
{

    /**
     * @var QueryBuilder
     */
    private $builder;

    public function setUp()
    {
        $this->builder = new QueryBuilder();
    }

    public function testUpdateQuery_WITH_WhereCondition()
    {
        $query = $this->builder->new('update_table')
            ->update([
                'name' => 'Test name',
                'age' => 25,
            ])
            ->where(['id' => 1])
            ->get();

        $expected = 'UPDATE update_table SET name = "Test name", age = 25 WHERE id = 1';

        $this->assertEquals($expected, $query);
    }

    public function testUpdateQuery_WITH_WhereCondition_AND_limit()
    {
        $query = $this->builder->new('update_table')
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
        $query = $this->builder->new('test_table')
            ->delete()
            ->where(['id' => 1])
            ->limit(1)
            ->get();

        $expected = 'DELETE FROM test_table WHERE id = 1 LIMIT 1';

        $this->assertEquals($expected, $query);
    }

    public function testInsertQuery()
    {
        $query = $this->builder->new('test_table_insert')
            ->insert(['name' => 'Test name', 'age' => 25])
            ->get();

        $expected = 'INSERT INTO test_table_insert (name, age) VALUES ("Test name", 25)';

        $this->assertEquals($expected, $query);
    }

    public function testSelectQuery()
    {
        $query = $this->builder->new('test_table_select')
            ->select()
            ->get();

        $expected = 'SELECT * FROM test_table_select';

        $this->assertEquals($expected, $query);
    }

    public function testSelectQuery_WITH_whereIdCondition()
    {
        $query = $this->builder->new('test_table_select')
            ->select()
            ->where(['id' => 1])
            ->get();

        $expected = 'SELECT * FROM test_table_select WHERE id = 1';

        $this->assertEquals($expected, $query);
    }

    public function testSelectQuery_WITH_whereIdCondition_AND_customFields()
    {
        $query = $this->builder->new('test_table_select')
            ->select(['id', 'name'])
            ->where(['id' => 1])
            ->get();

        $expected = 'SELECT id, name FROM test_table_select WHERE id = 1';

        $this->assertEquals($expected, $query);
    }

    public function testSelectQuery_WITH_whereIdCondition_AND_customFields_AND_orderByName()
    {
        $query = $this->builder->new('test_table_select')
            ->select(['id', 'name'])
            ->where(['id' => 1])
            ->order('name')
            ->get();

        $expected = 'SELECT id, name FROM test_table_select WHERE id = 1 ORDER BY name DESC';

        $this->assertEquals($expected, $query);
    }

    public function testSelectQuery_WITH_whereIdCondition_AND_customFields_AND_orderByNameAsc()
    {
        $query = $this->builder->new('test_table_select')
            ->select(['id', 'name'])
            ->where(['id' => 1])
            ->order('name', 'ASC')
            ->get();

        $expected = 'SELECT id, name FROM test_table_select WHERE id = 1 ORDER BY name ASC';

        $this->assertEquals($expected, $query);
    }

}