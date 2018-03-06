<?php

namespace SecureMy\Fragments;


use SecureMy\Expressions\Expression;
use SecureMy\QueryBuilder;
use SecureMy\Security;

class SelectFragment extends QueryBuilder
{
    protected $select;
    protected $alias;

    public function __construct(QueryBuilder $prev, $select, string $alias = null)
    {
        if(!$select instanceof Expression) {
            Security::validateIdentifier($select);
        }
        if($alias !== null) {
            Security::validateIdentifier($alias);
        }
        parent::__construct($prev);
        $this->select = $select;
        $this->alias = $alias;
    }

    public function __toString()
    {
        $select = $this->select instanceof Expression ? $this->select : "`{$this->select}`";
        $alias = $this->alias !== null ? " AS `{$this->alias}`" : "";
        return $select . $alias;
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
        return [];
    }
}
