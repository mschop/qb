<?php

namespace SecureMy;

use SecureMy\Expressions\AndExpression;
use SecureMy\Expressions\ColumnExpression;
use SecureMy\Expressions\EqExpression;
use SecureMy\Expressions\Expression;
use SecureMy\Expressions\NotExpression;
use SecureMy\Expressions\OrExpression;
use SecureMy\Expressions\ParamExpression;
use SecureMy\Fragments\FromFragment;
use SecureMy\Fragments\FullOuterJoinFragment;
use SecureMy\Fragments\GroupByFragment;
use SecureMy\Fragments\InnerJoinFragment;
use SecureMy\Fragments\LeftOuterJoinFragment;
use SecureMy\Fragments\OrderByFragment;
use SecureMy\Fragments\RightOuterJoinFragment;
use SecureMy\Fragments\SelectFragment;
use SecureMy\Fragments\WhereFragment;

/**
 * Class QueryBuilder
 *
 * The QueryBuilder class is the main class for building queries and acts like a Fragment and Expression factory.
 *
 * @package SecureMy
 */
class QueryBuilder
{
    const INDENTATION = '   ';
    const LINEBREAK = "\n";

    /**
     * Fragments are chained instead of collected in collections (like many other query builder do)
     * This variable hold the previous QueryBuild Object in the chain.
     *
     * @var QueryBuilder|null
     */
    protected $prev;

    protected $values = [];

    /**
     * QueryBuilder constructor.
     * @param QueryBuilder|null $prev
     */
    public function __construct(QueryBuilder $prev = null)
    {
        $this->prev = $prev;
    }

    /**
     * Creates the from fragment
     *
     * @param string $table
     * @param string|null $alias
     * @return FromFragment
     */
    public function from(string $table, string $alias = null): FromFragment
    {
        return new FromFragment($this, $table, $alias);
    }

    /**
     * Returns a new select fragment
     *
     * @param string $select
     * @param string|null $alias
     * @return SelectFragment
     */
    public function select(string $select, string $alias = null): SelectFragment
    {
        return new SelectFragment($this, $select, $alias);
    }

    public function join(string $table, Expression $condition, string $alias = null): InnerJoinFragment
    {
        return $this->innerJoin($table, $condition, $alias);
    }

    public function innerJoin(string $table, Expression $condition, string $alias = null): InnerJoinFragment
    {
        return new InnerJoinFragment($this, $table, $condition, $alias);
    }

    public function leftJoin(string $table, Expression $condition, string $alias = null): LeftOuterJoinFragment
    {
        return new LeftOuterJoinFragment($this, $table, $condition, $alias);
    }

    public function leftOuterJoin(string $table, Expression $condition, string $alias = null): LeftOuterJoinFragment
    {
        return $this->leftJoin($table, $condition, $alias);
    }

    public function rightJoin(string $table, Expression $condition, string $alias = null): RightOuterJoinFragment
    {
        return new RightOuterJoinFragment($this, $table, $condition, $alias);
    }

    public function rightOuterJoin(string $table, Expression $condition, string $alias = null): RightOuterJoinFragment
    {
        return $this->rightJoin($table, $condition, $alias);
    }

    public function fullOuterJoin(string $table, Expression $condition, string $alias = null): FullOuterJoinFragment
    {
        return new FullOuterJoinFragment($this, $table, $condition, $alias);
    }

    public function where($expression): WhereFragment
    {
        return new WhereFragment($this, $expression);
    }

    public function groupBy($groupBy): GroupByFragment
    {
        return new GroupByFragment($this, $groupBy);
    }

    public function orderBy($orderBy): OrderByFragment
    {
        return new OrderByFragment($this, $orderBy);
    }

    public function and (): AndExpression
    {
        $allExpressions = func_get_args();
        $last = array_pop($allExpressions);
        $allExpressions = array_reverse($allExpressions);
        foreach ($allExpressions as $expression) {
            $last = new AndExpression([$last, $expression]);
        }
        return $last;
    }

    public function or (): OrExpression
    {
        $allExpressions = func_get_args();
        $last = array_pop($allExpressions);
        $allExpressions = array_reverse($allExpressions);
        foreach ($allExpressions as $expression) {
            $last = new OrExpression([$last, $expression]);
        }
        return $last;
    }

    public function not($operand): NotExpression
    {
        return new NotExpression([$operand]);
    }

    public function eq($operand1, $operand2)
    {
        return new EqExpression([$operand1, $operand2]);
    }

    public function column(string $tableOrColumn, string $column = null)
    {
        return new ColumnExpression($tableOrColumn, $column);
    }

    public function param(string $name)
    {
        return new ParamExpression($name);
    }

    public function build(): BuildResult
    {
        // GROUP FRAGMENTS

        $groupedFragments = [];
        $cur = $this;
        do {
            $curClass = get_class($cur);
            if (!isset($groupedFragments[$curClass])) {
                $groupedFragments[$curClass] = [];
            }
            $groupedFragments[$curClass][] = $cur;
            $cur = $cur->prev;
        } while ($cur);

        if (!isset($groupedFragments[FromFragment::class])) {
            throw new InvalidBuilderStateException("No from fragment specified");
        }


        // BUILD QUERY

        $query = '';

        $query .= "SELECT" . self::LINEBREAK . self::INDENTATION;
        $query .= implode("," . self::LINEBREAK . self::INDENTATION, $groupedFragments[SelectFragment::class]);
        $query .= self::LINEBREAK;
        $query .= $groupedFragments[FromFragment::class][0];
        if (isset($groupedFragments[InnerJoinFragment::class])) {
            $query .= self::LINEBREAK;
            $query .= implode(self::LINEBREAK, $groupedFragments[InnerJoinFragment::class]);
        }
        if (isset($groupedFragments[LeftOuterJoinFragment::class])) {
            $query .= self::LINEBREAK;
            $query .= implode(self::LINEBREAK, $groupedFragments[LeftOuterJoinFragment::class]);
        }
        if (isset($groupedFragments[RightOuterJoinFragment::class])) {
            $query .= self::LINEBREAK;
            $query .= implode(self::LINEBREAK, $groupedFragments[RightOuterJoinFragment::class]);
        }
        if (isset($groupedFragments[FullOuterJoinFragment::class])) {
            $query .= self::LINEBREAK;
            $query .= implode(self::LINEBREAK, $groupedFragments[FullOuterJoinFragment::class]);
        }
        if (isset($groupedFragments[OrderByFragment::class])) {
            $query .= self::LINEBREAK . 'ORDER BY ';
            $query .= implode(', ', array_reverse($groupedFragments[OrderByFragment::class]));
        }
        if (isset($groupedFragments[GroupByFragment::class])) {
            $query .= self::LINEBREAK . "GROUP BY ";
            $query .= implode(', ', array_reverse($groupedFragments[GroupByFragment::class]));
        }
        if (isset($groupedFragments[WhereFragment::class])) {
            $query .= self::LINEBREAK . "WHERE ";
            $query .= implode(self::LINEBREAK . "AND", $groupedFragments[WhereFragment::class]);
        }


        // BUILD PARAMS
        $params = [];
        foreach($groupedFragments as $group) {
            foreach($group as $fragment) {
                foreach($fragment->get)
            }
        }


        return new BuildResult($query, []);
    }

    protected function getValues()
    {
        return $this->values;
    }
}
