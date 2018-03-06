<?php
/**
 * @copyright (c) JTL-Software-GmbH
 * @license http://jtl-url.de/jtlshoplicense
 */

namespace SecureMy\Fragments;


use SecureMy\Expressions\ColumnExpression;
use SecureMy\QueryBuilder;
use SecureMy\Security;

class OrderByFragment extends QueryBuilder
{
    protected $orderBy;


    /**
     * OrderByFragment constructor.
     * @param QueryBuilder $prev
     * @param string       $orderBy
     */
    public function __construct(QueryBuilder $prev, string $orderBy)
    {
        $orderBy   = trim($orderBy);
        $exploded = explode(' ', $orderBy);
        $final = [];
        foreach($exploded as $splinter) {
            if(!empty(trim($splinter))) {
                $final[] = trim($splinter);
            }
        }
        if(count($final) > 2) {
            throw new \InvalidArgumentException('Invalid order by');
        }
        Security::validateIdentifier($final[0]);
        $this->orderBy = '`' . $final[0] . '`';

        if(isset($final[1])) {
            $direction = strtoupper($final[1]);
            if($direction !== 'ASC' && $direction !== 'DESC') {
                throw new \InvalidArgumentException('Invalid direction in order by');
            }
            $this->orderBy .= ' ' . $direction;
        }
        parent::__construct($prev);
    }

    public function __toString()
    {
        return $this->orderBy;
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
