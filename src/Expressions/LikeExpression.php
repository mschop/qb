<?php

namespace SecureMy\Expressions;


class LikeExpression extends Expression
{
    public function __toString()
    {
        return "({$this->operands[0]} LIKE {$this->operands[1]})";
    }
}
