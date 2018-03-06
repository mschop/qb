<?php
/**
 * @copyright (c) JTL-Software-GmbH
 * @license http://jtl-url.de/jtlshoplicense
 */

namespace SecureMy\Fragments;


use SecureMy\QueryBuilder;

class StartFragment extends QueryBuilder
{
    public function __toString()
    {
        return '';
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
