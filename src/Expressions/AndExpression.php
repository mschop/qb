<?php

namespace ComposableQB\Expressions;


class AndExpression extends BinaryExpression
{
    public function __toString()
    {
        return "({$this->operand1}) AND ({$this->operand2})";
    }
}