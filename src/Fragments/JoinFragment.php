<?php

namespace SecureMy\Fragments;


use SecureMy\Expressions\Expression;
use SecureMy\QueryBuilder;
use SecureMy\Security;

class JoinFragment extends QueryBuilder
{
    protected $type;
    protected $table;
    protected $condition;
    protected $alias;

    public function __construct(
        QueryBuilder $prev,
        string $type,
        string $table,
        Expression $condition,
        string $alias = null
    ) {
        parent::__construct($prev);
        Security::validateIdentifier($table);
        if ($type !== 'LEFT' && $type !== 'RIGHT' && $type !== 'INNER') {
            throw new \InvalidArgumentException();
        }
        $this->type      = $type;
        $this->table     = $table;
        $this->condition = $condition;
        $this->alias     = $alias;
    }

    public function __toString()
    {
        $table = "`{$this->table}`";
        $alias = $this->alias === null ? '' : "AS `{$this->alias}`";

        return "{$this->type} JOIN $table $alias ON {$this->condition}";
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
