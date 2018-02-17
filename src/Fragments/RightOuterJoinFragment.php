<?php

namespace ComposableQB\Fragments;


use ComposableQB\Expressions\Expression;
use ComposableQB\QueryBuilder;
use ComposableQB\Security;

class RightOuterJoinFragment extends QueryBuilder
{
    protected $table;
    protected $condition;
    protected $alias;

    public function __construct(QueryBuilder $prev, string $table, Expression $condition, string $alias = null) {
        Security::validateIdentifier($table);
        if($alias !== null) {
            Security::validateIdentifier($alias);
        }
        parent::__construct($prev);
        $this->table = $table;
        $this->condition = $condition;
        $this->alias = $alias;
    }

    public function __toString()
    {
        $alias = $this->alias === null ? '' : "AS `{$this->alias}`";
        return "RIGHT JOIN `{$this->table}` $alias ON {$this->condition}";
    }
}