<?php

namespace SecureMy\Fragments;

use SecureMy\QueryBuilder;
use SecureMy\Security;

class GroupByFragment extends QueryBuilder
{
    protected $groupBy;

    /**
     * GroupByFragment constructor.
     * @param QueryBuilder $prev
     * @param string       $groupBy
     */
    public function __construct(QueryBuilder $prev, string $groupBy)
    {
        Security::validateIdentifier($groupBy);
        $this->groupBy = $groupBy;
        parent::__construct($prev);
    }

    public function __toString()
    {
        return '`' . $this->groupBy . '`';
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
