<?php

namespace ComposableQB\Fragments;


use ComposableQB\Expressions\Expression;
use ComposableQB\QueryBuilder;
use ComposableQB\Security;

class InnerJoinFragment extends QueryBuilder
{
    protected $table;
    protected $condition;
    protected $alias;

    public function __construct(QueryBuilder $prev, string $table, Expression $condition, string $alias = null)
    {
        Security::validateIdentifier($table);
        parent::__construct($prev);
        $this->table = $table;
        $this->condition = $condition;
        $this->alias = $alias;
    }

    public function __toString()
    {
        $table = "`{$this->table}`";
        $alias = $this->alias === null ? '' : "AS `{$this->alias}`";
        return "JOIN $table $alias ON {$this->condition}";
    }
}