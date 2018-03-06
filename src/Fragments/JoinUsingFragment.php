<?php
/**
 * @copyright (c) JTL-Software-GmbH
 * @license http://jtl-url.de/jtlshoplicense
 */

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
     * @param string $table
     * @param string $using
     * @param string $alias
     */
    public function __construct(QueryBuilder $prev, string $table, string $using, string $alias = null)
    {
        $this->table = $table;
        $this->alias = $alias;
        Security::validateIdentifier($table);
        if(is_array($using)) {
            foreach($using as $colName) {
                Security::validateIdentifier($colName);
            }
            $this->using = implode(', ', $using);
        } else {
            Security::validateIdentifier($using);
        }
        if($alias !== null) {
            Security::validateIdentifier($alias);
            $this->using = $using;
        }
        parent::__construct($prev);
    }


    public function __toString()
    {
        $table = "`{$this->table}`";
        $alias = $this->alias === null ? '' : "AS `{$this->alias}`";
        return "JOIN $table $alias USING ";
    }

    protected function getValues()
    {
        // TODO: Implement getValues() method.
    }

    protected function getExpressions()
    {
        // TODO: Implement getExpressions() method.
    }

}
