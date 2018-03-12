<?php

namespace SecureMy\Fragments;


use SecureMy\Expressions\Expression;
use SecureMy\QueryBuilder;
use SecureMy\Security;

class JoinUsingFragment extends QueryBuilder
{
    protected $table;
    protected $using;
    protected $alias;

    /**
     * JoinUsingFragment constructor.
     * @param QueryBuilder $prev
     * @param string       $type
     * @param string       $table
     * @param string       $using
     * @param string       $alias
     */
    public function __construct(QueryBuilder $prev, string $type, string $table, string $using, string $alias = null)
    {
        parent::__construct($prev);
        Security::validateIdentifier($table);
        $this->table = $table;
        $this->alias = $alias;

        if (is_array($using)) {
            foreach ($using as $colName) {
                Security::validateIdentifier($colName);
            }
            $this->using = '`' . implode('`, `', $using) . '`';
        } else {
            Security::validateIdentifier($using);
            $this->using = '`' . $using . '`';
        }

        if ($alias !== null) {
            Security::validateIdentifier($alias);
        }

        if ($type !== 'LEFT' && $type !== 'RIGHT' && $type !== 'INNER') {
            throw new \InvalidArgumentException();
        }
    }


    public function __toString()
    {
        $table = "`{$this->table}`";
        $alias = $this->alias === null ? '' : "AS `{$this->alias}`";

        return "JOIN $table $alias USING ({$this->using})";
    }

    protected function getValues()
    {
        return null;
    }

    protected function getExpressions()
    {
        return null;
    }

}
