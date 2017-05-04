<?php

namespace ComposableQB\Expressions;


class EqExpression extends BinaryExpression
{
    public function __toString()
    {
        return "({$this->operand1}) = ({$this->operand2})";
    }
}