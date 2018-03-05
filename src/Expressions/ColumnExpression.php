<?php

namespace SecureMy\Expressions;

use SecureMy\Security;

class ColumnExpression extends Expression
{
    protected $tableOrColumn;
    protected $column;

    public function __construct(string $tableOrColumn, string $column = null)
    {
        $tableOrColumn = trim($tableOrColumn);
        $column = trim($column);
        Security::validateIdentifier($tableOrColumn);
        if($column !== null) {
            Security::validateIdentifier($column);
        }
        $this->tableOrColumn = $tableOrColumn;
        $this->column        = $column;
    }

    public function __toString()
    {
        $result = '`' . $this->tableOrColumn . '`';
        if($this->column !== null) {
            $result .= '.`' . $this->column . '`';
        }
        return $result;
    }

}
