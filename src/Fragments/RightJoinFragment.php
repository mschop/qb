<?php

namespace SecureMy\Fragments;


use SecureMy\Expressions\Expression;
use SecureMy\QueryBuilder;
use SecureMy\Security;

class RightJoinFragment extends QueryBuilder
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

    /**
     * @inheritdoc
     */
    protected function getValues()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    protected function getExpressions()
    {
        return [$this->condition];
    }
}
