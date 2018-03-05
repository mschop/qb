<?php
/**
 * @copyright (c) JTL-Software-GmbH
 * @license http://jtl-url.de/jtlshoplicense
 */

namespace SecureMy\Fragments;


use SecureMy\Expressions\ColumnExpression;
use SecureMy\QueryBuilder;

class GroupByFragment extends QueryBuilder implements FragmentInterface
{
    protected $column;

    /**
     * GroupByFragment constructor.
     * @param QueryBuilder     $prev
     * @param ColumnExpression $column
     */
    public function __construct(QueryBuilder $prev, ColumnExpression $column)
    {
        parent::__construct($prev);
        $this->column = $column;
    }

    public function __toString()
    {
        return (string)$this->column;
    }
}
