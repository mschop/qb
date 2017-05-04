<?php

namespace ComposableQB\Expressions;


use ComposableQB\Security;

class ParamExpression extends Expression
{
    const PARAMS_VALIDATION_REGEX = '/^[a-z0-9_]+$/i';

    protected $name;

    public function __construct($name)
    {
        Security::validateIdentifier($name);
        $this->name = $name;
    }

    public function __toString()
    {
        return substr($this->name, 0, 1) === ':' ? $this->name : ':' . $this->name;
    }
}