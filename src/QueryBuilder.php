<?php

namespace ComposableQB;

use ComposableQB\Expressions\AndExpression;
use ComposableQB\Expressions\ColumnExpression;
use ComposableQB\Expressions\EqExpression;
use ComposableQB\Expressions\Expression;
use ComposableQB\Expressions\NotExpression;
use ComposableQB\Expressions\OrExpression;
use ComposableQB\Expressions\ParamExpression;
use ComposableQB\Fragments\FromFragment;
use ComposableQB\Fragments\FullOuterJoinFragment;
use ComposableQB\Fragments\InnerJoinFragment;
use ComposableQB\Fragments\LeftOuterJoinFragment;
use ComposableQB\Fragments\RightOuterJoinFragment;
use ComposableQB\Fragments\SelectFragment;
use ComposableQB\Fragments\WhereFragment;

/**
 * Class QueryBuilder
 *
 * The QueryBuilder class is the main class for building queries and acts like a Fragment and Expression factory.
 *
 * @package ComposableQB
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

    public function column(string $table, string $column)
    {
        return new ColumnExpression($table, $column);
    }

    public function param(string $name)
    {
        return new ParamExpression($name);
    }

    public function build(): BuildResult
    {
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
        if (isset($groupedFragments[WhereFragment::class])) {
            $query .= self::LINEBREAK . "WHERE ";
            $query .= implode(self::LINEBREAK . "AND", $groupedFragments[WhereFragment::class]);
        }

        return new BuildResult($query, []);
    }
}