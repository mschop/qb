<?php
/**
 * @copyright (c) JTL-Software-GmbH
 * @license http://jtl-url.de/jtlshoplicense
 */

namespace SecureMy\Fragments;


use SecureMy\Expressions\ColumnExpression;
use SecureMy\QueryBuilder;

class OrderByFragment extends QueryBuilder implements FragmentInterface
{
    protected $column;
    protected $direction;


    /**
     * OrderByFragment constructor.
     */
    public function __construct(QueryBuilder $prev, ColumnExpression $column, string $direction)
    {
        $direction = strtoupper($direction);
        if($direction !== 'ASC' && $direction !== 'DESC') {
            throw new \InvalidArgumentException('Order direction must be ASC or DESC');
        }
        $this->column = $column;
        $this->direction = $direction;
    }

    public function __toString()
    {
        return $this->column . ' ' . $this->direction;
    }
}
