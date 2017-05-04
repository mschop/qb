<?php

namespace ComposableQB\Fragments;


use ComposableQB\QueryBuilder;

class WhereFragment extends QueryBuilder
{
    protected $expression;

    public function __construct(QueryBuilder $prev, $expression)
    {
        parent::__construct($prev);
        $this->expression = $expression;
    }

    public function __toString()
    {
        return (string)$this->expression;
    }
}