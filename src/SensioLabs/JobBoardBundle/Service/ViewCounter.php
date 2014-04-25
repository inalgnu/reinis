<?php

namespace SensioLabs\JobBoardBundle\Service;

class ViewCounter
{
    private $redis;

    public function __construct($redis)
    {
        $this->redis = $redis;
    }

    /**
     * @param $alias For example the object-type
     * @param $id Id of the object
     * @param $route
     */
    public function incrementViewForRoute($alias, $id, $route)
    {
        $this->redis->incr($alias.':'.$id.':'.$route);
        $this->redis->sadd($alias.':'.$id.':routes', $route);
    }

    /**
     * @param $alias
     * @param $id
     * @param $route
     * @return mixed
     */
    public function getViewForRoute($alias, $id, $route)
    {
        return $this->redis->get($alias.':'.$id.':'.$route);
    }

    /**
     * @param $alias
     * @param $id
     * @return mixed
     */
    public function getRoutes($alias, $id)
    {
        return $this->redis->smembers($alias.':'.$id.':routes');
    }

    /**
     * @param $alias
     * @param $id
     * @param $route
     */
    public function deleteViewForRoute($alias, $id, $route)
    {
        $this->redis->del($alias.':'.$id.':'.$route);
        $this->redis->srem($alias.':'.$id.':routes', $route);
    }
}
