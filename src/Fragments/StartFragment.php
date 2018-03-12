<?php

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
