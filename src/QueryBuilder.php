<?php

namespace ComposableQB;

use ComposableQB\Expressions\AndExpression;
use ComposableQB\Expressions\Expression;
use ComposableQB\Expressions\NotExpression;
use ComposableQB\Expressions\OrExpression;
use ComposableQB\Fragments\FromFragment;
use ComposableQB\Fragments\FullOuterJoinFragment;
use ComposableQB\Fragments\InnerJoinFragment;
use ComposableQB\Fragments\LeftOuterJoinFragment;
use ComposableQB\Fragments\RightOuterJoinFragment;
use ComposableQB\Fragments\SelectFragment;
use ComposableQB\Fragments\WhereFragment;

class QueryBuilder
{
    const INTENTION = '   ';
    const LINEBREAK = "\n";

    protected $prev = null;

    protected function __construct(QueryBuilder $prev)
    {
        $this->prev = $prev;
    }

    public static function from(string $table)
    {
        return new FromFragment($table);
    }

    public function select(string $select, string $alias = null) : SelectFragment
    {
        return new SelectFragment($this, $select, $alias);
    }

    public function join(string $table, string $condition, string $alias = null) : InnerJoinFragment
    {
        return $this->innerJoin($table, $condition, $alias);
    }

    public function innerJoin(string $table, string $condition, string $alias = null) : InnerJoinFragment
    {
        return new InnerJoinFragment($this, $table, $condition, $alias);
    }

    public function leftJoin(string $table, string $condition, string $alias = null) : LeftOuterJoinFragment
    {
        return new LeftOuterJoinFragment($this, $table, $condition, $alias);
    }

    public function leftOuterJoin(string $table, string $condition, string $alias = null) : LeftOuterJoinFragment
    {
        return $this->leftJoin($table, $condition, $alias);
    }

    public function rightJoin(string $table, string $condition, string $alias = null) : RightOuterJoinFragment
    {
        return new RightOuterJoinFragment($this, $table, $condition, $alias);
    }

    public function rightOuterJoin(string $table, string $condition, string $alias = null) : RightOuterJoinFragment
    {
        return $this->rightJoin($table, $condition, $alias);
    }

    public function fullOuterJoin(string $table, string $condition, string $alias = null) : FullOuterJoinFragment
    {
        return new FullOuterJoinFragment($this, $table, $condition, $alias);
    }

    public function where($expression) : WhereFragment
    {
        return new WhereFragment($this, $expression);
    }

    public function exprAnd() : AndExpression
    {
        $allExpressions = func_get_args();
        $last = array_pop($allExpressions);
        $allExpressions = array_reverse($allExpressions);
        foreach($allExpressions as $expression) {
            $last = new AndExpression($last, $expression);
        }
        return $last;
    }

    public function exprOr() : OrExpression
    {
        $allExpressions = func_get_args();
        $last = array_pop($allExpressions);
        $allExpressions = array_reverse($allExpressions);
        foreach($allExpressions as $expression) {
            $last = new OrExpression($last, $expression);
        }
        return $last;
    }

    public function exprNot(Expression $operand) : NotExpression
    {
        return new NotExpression($operand);
    }

    public function getQuery() : string
    {
        $groupedFragments = [];
        $cur = $this;
        do {
            $curClass = get_class($cur);
            if(!isset($groupedFragments[$curClass])) {
                $groupedFragments[$curClass] = [];
            }
            $groupedFragments[$curClass][] = $cur;
            $cur = $cur->prev;
        } while ($cur);

        if(!isset($groupedFragments[FromFragment::class])) {
            throw new IncompleteSQLException("No from fragment specified");
        }

        $query = '';

        $query .= "SELECT" . self::LINEBREAK . self::INTENTION;
        $query .= implode("," . self::LINEBREAK . self::INTENTION, $groupedFragments[SelectFragment::class]);
        $query .= self::LINEBREAK;
        $query .= $groupedFragments[FromFragment::class][0];
        if(isset($groupedFragments[InnerJoinFragment::class])) {
            $query .= self::LINEBREAK;
            $query .= implode(self::LINEBREAK, $groupedFragments[InnerJoinFragment::class]);
        }
        if(isset($groupedFragments[LeftOuterJoinFragment::class])) {
            $query .= self::LINEBREAK;
            $query .= implode(self::LINEBREAK, $groupedFragments[LeftOuterJoinFragment::class]);
        }
        if(isset($groupedFragments[RightOuterJoinFragment::class])) {
            $query .= self::LINEBREAK;
            $query .= implode(self::LINEBREAK, $groupedFragments[RightOuterJoinFragment::class]);
        }
        if(isset($groupedFragments[FullOuterJoinFragment::class])) {
            $query .= self::LINEBREAK;
            $query .= implode(self::LINEBREAK, $groupedFragments[FullOuterJoinFragment::class]);
        }
        if(isset($groupedFragments[WhereFragment::class])) {
            $query .= self::LINEBREAK . "WHERE ";
            $query .= implode(self::LINEBREAK . "AND", $groupedFragments[WhereFragment::class]);
        }

        return $query;
    }
}