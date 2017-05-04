<?php

namespace ComposableQB\Fragments;


use ComposableQB\QueryBuilder;

class FullOuterJoinFragment extends QueryBuilder
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
        return 'FULL OUTER JOIN ' . $this->table . ($this->alias === null ? '' : ' AS ' . $this->alias) . ' ON ' . $this->condition;
    }
}