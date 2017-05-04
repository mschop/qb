<?php

namespace ComposableQB\Fragments;


use ComposableQB\QueryBuilder;

class SelectFragment extends QueryBuilder
{
    protected $select;
    protected $alias;

    public function __construct(QueryBuilder $prev, string $select, string $alias = null)
    {
        parent::__construct($prev);
        $this->select = $select;
        $this->alias = $alias;
    }

    public function __toString()
    {
        return $this->alias === null ? $this->select : $this->select . ' AS ' . $this->alias;
    }
}