<?php

namespace ComposableQB\Expressions;


class NotExpression extends SingularExpression
{
    public function __toString()
    {
        return "NOT ({$this->operand})";
    }
}