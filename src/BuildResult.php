<?php

namespace ComposableQB;


class BuildResult
{
    protected $query;
    protected $params;

    /**
     * BuildResult constructor.
     * @param $query
     * @param $params
     */
    public function __construct($query, $params)
    {
        $this->query = $query;
        $this->params = $params;
    }

    /**
     * @return mixed
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @return mixed
     */
    public function getParams()
    {
        return $this->params;
    }
}