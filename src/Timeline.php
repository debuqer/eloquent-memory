<?php


namespace Debuqer\EloquentMemory;


class Timeline extends \SplPriorityQueue
{
    const ORDER_DESC = 'desc';
    const ORDER_ASC = 'asc';

    protected $order;

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
        return match($this->order) {
            null => $priority1->greaterThan($priority2) ? 0 : -1,
            'desc' => $priority1->greaterThan($priority2) ? 0 : -1,
            'asc' => $priority2->greaterThan($priority1) ? 0 : -1
        };
    }
}
