<?php

namespace ComposableQB\Fragments;


use ComposableQB\QueryBuilder;

class InnerJoinFragment extends QueryBuilder
{
    protected $table;
    protected $condition;
    protected $alias;

    public function __construct(QueryBuilder $prev, string $table, string $condition, string $alias = null)
    {
        parent::__construct($prev);
        $this->table = $table;
        $this->condition = $condition;
        $this->alias = $alias;
    }

    public function __toString()
    {
        return 'INNER JOIN ' . $this->table . ($this->alias === null ? '' : ' AS ' . $this->alias) . ' ON ' . $this->condition;
    }
}