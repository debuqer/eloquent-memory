<?php

namespace Debuqer\EloquentMemory;

class Timeline extends \SplPriorityQueue
{
    public const ORDER_DESC = 'desc';
    public const ORDER_ASC = 'asc';

    protected $order = 'desc';

    public function latestFirst()
    {
        $this->order = static::ORDER_DESC;

        return $this;
    }

    public function oldestFirst()
    {
        $this->order = static::ORDER_ASC;

        return $this;
    }

    public function compare($priority1, $priority2)
    {
        return ($this->order === static::ORDER_ASC ? -1 : 1 ) * strcmp($priority1, $priority2);
    }
}
