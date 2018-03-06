<?php

namespace SecureMy\Fragments;


use SecureMy\Expressions\Expression;
use SecureMy\QueryBuilder;
use SecureMy\Security;

class SelectFragment extends QueryBuilder
{
    protected $select;
    protected $alias;

    public function __construct(QueryBuilder $prev, $select, string $alias = null)
    {
        if($select instanceof Expression) {
            $this->select = $select;
        } else {
            $dotExploded = explode('.', $select);
            $dotExploded = array_map('trim', $dotExploded);
            foreach($dotExploded as $part) {
                if($part !== '*') {
                    Security::validateIdentifier($select);
                }
            }
            $this->select = $dotExploded;
        }

        if($alias !== null) {
            Security::validateIdentifier($alias);
        }
        parent::__construct($prev);
        $this->alias = $alias;
    }

    public function __toString()
    {
        if ($this->select instanceof Expression) {
            $select = $this->select;
        } else {
            $parts = [];
            foreach($this->select as $part) {
                if($part === '*') {
                    $parts[] = '*';
                } else {
                    $parts[] = '`' . $part . '`';
                }
            }
            $select = implode('.', $parts);
        }
        $alias = $this->alias !== null ? " AS `{$this->alias}`" : "";
        return $select . $alias;
    }

    /**
     * @inheritdoc
     */
    protected function getValues()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    protected function getExpressions()
    {
        return [];
    }

}
