<?php

namespace SecureMy\Expressions;


use SecureMy\Security;

class FuncExpression extends Expression
{
    protected $funcName;

    /**
     * FuncExpression constructor.
     * @param $funcName
     * @param array $operands
     */
    public function __construct(string $funcName, array $operands)
    {
        $this->funcName = $funcName;
        Security::validateFuncName($funcName);
        parent::__construct($operands);
    }


    public function __toString()
    {
        return $this->funcName . '(' . implode(', ', $this->operands) . ')';
    }
}
