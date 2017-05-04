<?php

namespace ComposableQB\Expressions;


abstract class Expression
{
    protected static $counter = 0;

    protected $values = [];
    protected $operands = [];

    public function __construct(array $operands)
    {
        foreach($operands as $operand) {
            if($operand instanceof Expression) {
                $this->operands[] = $operand;
            } else {
                $paramName = 'vev_' . static::$counter++;
                $this->values[$paramName] = $operand;
                $this->operands[] = new ParamExpression($paramName);
            }
        }
    }

    public abstract function __toString();

    public function getValues()
    {
        return $this->values;
    }
}