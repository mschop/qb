<?php

namespace ComposableQB\Expressions;


class NotExpression extends Expression
{
    public function __toString()
    {
        return "NOT {$this->operands[0]}";
    }
}