<?php

namespace SecureMy;

use SecureMy\Expressions\AndExpression;
use SecureMy\Expressions\ColumnExpression;
use SecureMy\Expressions\EqExpression;
use SecureMy\Expressions\Expression;
use SecureMy\Expressions\FuncExpression;
use SecureMy\Expressions\LikeExpression;
use SecureMy\Expressions\NotExpression;
use SecureMy\Expressions\OrExpression;
use SecureMy\Expressions\ParamExpression;
use SecureMy\Fragments\FromFragment;
use SecureMy\Fragments\FullJoinFragment;
use SecureMy\Fragments\GroupByFragment;
use SecureMy\Fragments\JoinFragment;
use SecureMy\Fragments\JoinUsingFragment;
use SecureMy\Fragments\LeftJoinFragment;
use SecureMy\Fragments\OrderByFragment;
use SecureMy\Fragments\RightJoinFragment;
use SecureMy\Fragments\SelectFragment;
use SecureMy\Fragments\StartFragment;
use SecureMy\Fragments\ValueFragment;
use SecureMy\Fragments\WhereFragment;

/**
 * Class QueryBuilder
 *
 * The QueryBuilder class is the main class for building queries and acts like a Fragment and Expression factory.
 *
 * @package SecureMy
 */
abstract class QueryBuilder
{
    const INDENTATION = '   ';
    const LINEBREAK = "\n";

    const INDEX_DEFAULT = -1;
    const INDEX_FROM = 0;
    const INDEX_SELECT = 1;
    const INDEX_JOIN = 2;
    const INDEX_JOIN_USING = 3;
    const INDEX_GROUP = 4;
    const INDEX_ORDER = 5;
    const INDEX_WHERE = 6;

    /**
     * Fragments are chained instead of collected in collections (like many other query builder do)
     * This variable hold the previous QueryBuild Object in the chain.
     *
     * @var QueryBuilder|null
     */
    protected $prev;

    public static $one = 0;
    public static $two = 0;

    /**
     * QueryBuilder constructor.
     * @param QueryBuilder|null $prev
     */
    protected function __construct(QueryBuilder $prev = null)
    {
        $this->prev = $prev;
    }

    public static function create()
    {
        return new StartFragment();
    }

    /**
     * Return the necessary information, needed to execute the built query.
     *
     * @return string
     * @throws InvalidBuilderStateException
     */
    public function getQuery(): string
    {
        // GROUP FRAGMENTS
        $fragments = new \SplFixedArray(7);
        for($x = 0; $x < 7; $x++) {
            $fragments[$x] = '';
        }
        $cur       = $this;
        do {
            $index = $this->mapObjectToIndex($cur);
            if($index === self::INDEX_DEFAULT) {
                $cur = $cur->prev;
                continue;
            }
            if (empty($fragments[$index])) {
                $fragments[$index] .= $cur;
            } else {
                $add = '';

                if (
                    $cur instanceof SelectFragment
                    || $cur instanceof GroupByFragment
                    || $cur instanceof OrderByFragment
                ) {
                    $add .= ',';
                }

                if (
                    $cur instanceof JoinUsingFragment
                    || $cur instanceof JoinFragment
                    || $cur instanceof WhereFragment
                    || $cur instanceof SelectFragment
                ) {
                    $add .= self::LINEBREAK;
                }

                if ($cur instanceof WhereFragment) {
                    $add .= 'AND';
                }

                $fragments[$index] .= $add . $cur;
            }
            $cur = $cur->prev;
        } while ($cur);


        // BUILD QUERY

        $query = self::LINEBREAK . 'SELECT' . self::LINEBREAK . $fragments[self::INDEX_SELECT];
        if(empty($fragments[self::INDEX_FROM])) {
            throw new InvalidBuilderStateException('From is missing');
        }

        $query .= self::LINEBREAK;
        $query .= $fragments[self::INDEX_FROM];

        if(!empty($fragments[self::INDEX_JOIN_USING])) {
            $query .= self::LINEBREAK;
            $query .= $fragments[self::INDEX_JOIN_USING];
        }

        if(!empty($fragments[self::INDEX_JOIN])) {
            $query .= self::LINEBREAK;
            $query .= $fragments[self::INDEX_JOIN];
        }

        if(!empty($fragments[self::INDEX_WHERE])) {
            $query .= self::LINEBREAK;
            $query .= 'WHERE ';
            $query .= $fragments[self::INDEX_WHERE];
        }

        if(!empty($fragments[self::INDEX_GROUP])) {
            $query .= self::LINEBREAK;
            $query .= 'GROUP BY ';
            $query .= $fragments[self::INDEX_GROUP];
        }
        if(!empty($fragments[self::INDEX_ORDER])) {
            $query .= self::LINEBREAK;
            $query .= 'ORDER BY ';
            $query .= $fragments[self::INDEX_ORDER];
        }

        return $query;
    }

    protected function mapObjectToIndex($obj)
    {
        if($obj instanceof FromFragment) {
            return self::INDEX_FROM;
        } elseif($obj instanceof SelectFragment) {
            return self::INDEX_SELECT;
        } elseif($obj instanceof JoinFragment) {
            return self::INDEX_JOIN;
        } elseif($obj instanceof JoinUsingFragment) {
            return self::INDEX_JOIN_USING;
        } elseif($obj instanceof GroupByFragment) {
            return self::INDEX_GROUP;
        } elseif($obj instanceof OrderByFragment) {
            return self::INDEX_ORDER;
        } elseif($obj instanceof WhereFragment) {
            return self::INDEX_WHERE;
        }
        return self::INDEX_DEFAULT;
    }

    public function getParams()
    {
        $cur    = $this;
        $params = [];
        do {
            $expression = $cur->getExpressions();
            if($expression !== null) {
                foreach ($cur->getExpressions() as $expression) {
                    $params = array_merge($params, $expression->getValues());
                }
            }
            $values = $cur->getValues();
            if($values !== null) {
                $params = array_merge($params, $cur->getValues());
            }
            $cur    = $cur->prev;
        } while ($cur);

        return $params;
    }

    public abstract function __toString();

    /**
     * Fetches parameter values, that are bound with a fragment (mainly ValueFragment)
     *
     * @return array|null
     */
    protected abstract function getValues();

    /**
     * Fetches all expression on level 1 in a fragment
     *
     * @return Expression[]|null
     */
    protected abstract function getExpressions();


    // FRAGMENTS

    /**
     * Creates the from fragment
     *
     * @param string      $table
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
     * @param string|Expression $select
     * @param string|null       $alias
     * @return SelectFragment
     */
    public function select($select, string $alias = null): SelectFragment
    {
        return new SelectFragment($this, $select, $alias);
    }

    public function selectMany(array $selects): SelectFragment
    {
        $last = $this;
        foreach ($selects as $key => $value) {
            if (is_string($key) || $key instanceof Expression) {
                $last = new SelectFragment($last, $key, $value);
            } else {
                $last = new SelectFragment($last, $value);
            }
        }

        return $last;
    }

    public function join(string $table, Expression $condition, string $alias = null): JoinFragment
    {
        return new JoinFragment($this, 'INNER', $table, $condition, $alias);
    }

    /**
     * @param string       $table
     * @param string|array $using
     * @param string|null  $alias
     * @return JoinUsingFragment
     */
    public function joinUsing(string $table, $using, string $alias = null): JoinUsingFragment
    {
        return new JoinUsingFragment($this, 'INNER', $table, $using, $alias);
    }

    public function leftJoin(string $table, Expression $condition, string $alias = null): JoinFragment
    {
        return new JoinFragment($this, 'LEFT', $table, $condition, $alias);
    }

    public function leftJoinUsing(string $table, $using, string $alias = null): JoinUsingFragment
    {
        return new JoinUsingFragment($this, 'LEFT', $table, $using, $alias);
    }

    public function rightJoin(string $table, Expression $condition, string $alias = null): JoinFragment
    {
        return new JoinFragment($this, 'RIGHT', $table, $condition, $alias);
    }

    public function rightJoinUsing(string $table, $using, string $alias = null): JoinUsingFragment
    {
        return new JoinUsingFragment($this, 'RIGHT', $table, $using, $alias);
    }

    /**
     * @return WhereFragment
     */
    public function where(): WhereFragment
    {
        $args  = func_get_args();
        $count = count($args);
        if ($count > 1) {
            return new WhereFragment($this, call_user_func_array([$this, 'and'], $args));
        } elseif ($count = 0) {
            throw new \InvalidArgumentException("missing where condition");
        } else {
            return new WhereFragment($this, $args[0]);
        }
    }

    public function group($by): GroupByFragment
    {
        return new GroupByFragment($this, $by);
    }

    public function order($by): OrderByFragment
    {
        return new OrderByFragment($this, $by);
    }

    public function bind($key, $value): ValueFragment
    {
        return new ValueFragment($this, $key, $value);
    }

    public function bindMany($array): ValueFragment
    {
        $last = $this;
        foreach ($array as $key => $value) {
            $last = new ValueFragment($last, $key, $value);
        }

        return $last;
    }


    // EXPRESSIONS

    public function and (): AndExpression
    {
        $allExpressions = func_get_args();
        $last           = array_pop($allExpressions);
        $allExpressions = array_reverse($allExpressions);
        foreach ($allExpressions as $expression) {
            $last = new AndExpression([$last, $expression]);
        }

        return $last;
    }

    public function or (): OrExpression
    {
        $allExpressions = func_get_args();
        $last           = array_pop($allExpressions);
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

    public function like($operand1, $operand2)
    {
        return new LikeExpression([$operand1, $operand2]);
    }

    public function col(string $tableOrColumn, string $column = null)
    {
        return new ColumnExpression($tableOrColumn, $column);
    }

    public function param(string $name)
    {
        return new ParamExpression($name);
    }

    /**
     * @param string $funcName
     * @param array  ...$parameter
     * @return FuncExpression
     */
    public function func(string $funcName, ...$parameter): FuncExpression
    {
        return new FuncExpression($funcName, $parameter);
    }
}
