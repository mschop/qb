<?php

namespace ComposableQB\Fragments;


use ComposableQB\QueryBuilder;
use ComposableQB\Security;

class SelectFragment extends QueryBuilder
{
    protected $select;
    protected $alias;

    public function __construct(QueryBuilder $prev, string $select, string $alias = null)
    {
        Security::validateIdentifier($select);
        if($alias !== null) {
            Security::validateIdentifier($alias);
        }
        parent::__construct($prev);
        $this->select = $select;
        $this->alias = $alias;
    }

    public function __toString()
    {
        $select = "`{$this->select}`";
        $alias = $this->alias !== null ? " AS `{$this->alias}`" : "";
        return $select . $alias;
    }
}