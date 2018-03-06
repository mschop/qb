<?php

namespace SecureMy\Fragments;

use SecureMy\QueryBuilder;

class ValueFragment extends QueryBuilder
{
    protected $key;
    protected $value;

    /**
     * ValueFragment constructor.
     * @param QueryBuilder $prev
     * @param string $key
     * @param mixed $value
     */
    public function __construct(QueryBuilder $prev, string $key, $value)
    {
        $this->key   = $key;
        $this->value = $value;
        parent::__construct($prev);
    }


    public function __toString()
    {
        return '';
    }

    /**
     * @inheritdoc
     */
    protected function getValues()
    {
        return [$this->key => $this->value];
    }

    /**
     * @inheritdoc
     */
    protected function getExpressions()
    {
        return [];
    }
}
