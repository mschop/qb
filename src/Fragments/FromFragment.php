<?php

namespace ComposableQB\Fragments;


use ComposableQB\QueryBuilder;

class FromFragment extends QueryBuilder
{
    protected $table;

    public function __construct(string $table)
    {
        $this->table = $table;
    }

    public function __toString()
    {
        return 'FROM ' . $this->table;
    }
}