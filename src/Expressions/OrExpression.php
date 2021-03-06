<?php

namespace SecureMy\Expressions;


class OrExpression extends Expression
{
    public function __toString()
    {
        return '(' . implode(' OR ', $this->operands) . ')';
    }
}
