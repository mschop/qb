<?php

namespace ComposableQB;


class QueryBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        $qb = QueryBuilder::from('some_table');
        $sql = $qb
            ->select('some_column', 'some_alias')
            ->select('another_column', 'another_alias')
            ->join('another_table', 'some_table.another_id = ano.id', 'ano')
            ->getQuery();

        echo($sql);
    }
}
