<?php
/**
 * @copyright (c) JTL-Software-GmbH
 * @license http://jtl-url.de/jtlshoplicense
 */

namespace SecureMy\Fragments;

use SecureMy\QueryBuilder;
use SecureMy\Security;

class GroupByFragment extends QueryBuilder implements FragmentInterface
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
        return $this->groupBy;
    }
}
