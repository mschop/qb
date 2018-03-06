<?php

namespace SecureMy\Expressions;


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

    /**
     * Recursive fetch all param values (key => value)
     *
     * @return array
     */
    public function getValues()
    {
        $result = $this->values;
        foreach($this->operands as $operand) {
            $result = array_merge($result, $operand->getValues());
        }
        return $result;
    }
}
