<?php

namespace ComposableQB\Expressions;

use ComposableQB\Security;

class ColumnExpression extends Expression
{
    protected $table;
    protected $column;

    public function __construct(string $table, string $column)
    {
        Security::validateIdentifier($table);
        Security::validateIdentifier($column);
        $this->table = $table;
        $this->column = $column;
    }

    public function __toString()
    {
        return '`' . $this->table . '`.`' . $this->column . '`';
    }

}