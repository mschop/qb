<?php

namespace SecureMy\Fragments;


use SecureMy\QueryBuilder;
use SecureMy\Security;
use SecureMy\Security\IdentifierSecurityPolicyInterface;

class FromFragment extends QueryBuilder
{
    protected $table;
    protected $alias;

    public function __construct(QueryBuilder $prev, string $table, string $alias = null)
    {
        Security::validateIdentifier($table);
        parent::__construct($prev);
        $this->table = $table;
        $this->alias = $alias;
    }

    public function __toString()
    {
        $alias = $this->alias === null ? '' : "AS `{$this->alias}`";
        return "FROM `{$this->table}` $alias";
    }

    /**
     * @inheritdoc
     */
    protected function getValues()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    protected function getExpressions()
    {
        return null;
    }


}
