<?php


namespace Debuqer\EloquentMemory\Repositories;


class TransitionQuery
{
    const DEFAULT_TAKE = 100;
    const DEFAULT_ORDER = 'desc';
    const DEFAULT_ORDER_KEY = 'date_recorded';
    const DEFAULT_CONDITIONS = [];

    protected $batch;
    protected $before;
    protected $after;
    protected $until;
    protected $from;
    protected $take = self::DEFAULT_TAKE;
    protected $order = self::DEFAULT_ORDER;
    protected $orderKey = self::DEFAULT_ORDER_KEY;
    protected $conditions = self::DEFAULT_CONDITIONS;



    /**
     * @return mixed
     */
    public function getBatch()
    {
        return $this->batch;
    }

    /**
     * @param mixed $batch
     * @return TransitionQuery
     */
    public function setBatch($batch)
    {
        $this->batch = $batch;

        return $this;
    }

    /**
     * @return static
     */
    public static function create()
    {
        return new static;
    }

    public function isSeted($key)
    {
        return isset($this->{$key});
    }

    /**
     * @return mixed
     */
    public function getBefore()
    {
        return $this->before;
    }

    /**
     * @param $before
     * @return $this
     */
    public function setBefore($before)
    {
        $this->before = $before;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAfter()
    {
        return $this->after;
    }

    /**
     * @param $after
     * @return $this
     */
    public function setAfter($after)
    {
        $this->after = $after;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUntil()
    {
        return $this->until;
    }

    /**
     * @param $until
     * @return $this
     */
    public function setUntil($until)
    {
        $this->until = $until;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @param $from
     * @return $this
     */
    public function setFrom($from)
    {
        $this->from = $from;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTake()
    {
        return $this->take;
    }

    /**
     * @param $take
     * @return $this
     */
    public function setTake($take)
    {
        $this->take = $take;

        return $this;
    }

    /**
     * @return string
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param $order
     * @return $this
     */
    public function setOrder($order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * @return string
     */
    public function getOrderKey()
    {
        return $this->orderKey;
    }

    /**
     * @param string $orderKey
     * @return TransitionQuery
     */
    public function setOrderKey($orderKey)
    {
        $this->orderKey = $orderKey;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getConditions()
    {
        return $this->conditions;
    }

    /**
     * @param $conditions
     * @return $this
     */
    public function setConditions($conditions)
    {
        $this->conditions = $conditions;

        return $this;
    }


}
