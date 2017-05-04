<?php

namespace ComposableQB\Expressions;


class OrExpression extends BinaryExpression
{
    public function __toString()
    {
        return "({$this->operand1}) OR ({$this->operand2})";
    }
}