<?php

namespace SecureMy\Expressions;


class NotExpression extends Expression
{
    public function __toString()
    {
        return "NOT {$this->operands[0]}";
    }
}
