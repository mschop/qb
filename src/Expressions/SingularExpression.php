<?php

namespace ComposableQB\Expressions;


abstract class SingularExpression extends Expression
{
    protected $operand;

    /**
     * SingularExpression constructor.
     * @param Expression $operand
     */
    public function __construct(Expression $operand)
    {
        $this->operand = $operand;
    }
}