<?php

namespace ComposableQB\Expressions;


abstract class BinaryExpression extends Expression
{
    protected $operand1;
    protected $operand2;

    /**
     * BinaryOperator constructor.
     * @param $operand1
     * @param $operand2
     */
    public function __construct(Expression $operand1, Expression $operand2)
    {
        $this->operand1 = $operand1;
        $this->operand2 = $operand2;
    }
}