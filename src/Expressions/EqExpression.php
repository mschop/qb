<?php

namespace ComposableQB\Expressions;


class EqExpression extends Expression
{
    public function __toString()
    {
        return "({$this->operands[0]} = {$this->operands[1]})";
    }
}