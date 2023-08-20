<?php

namespace Debuqer\EloquentMemory\Repositories;

use Debuqer\EloquentMemory\Timeline;
use Debuqer\EloquentMemory\Transitions\TransitionInterface;

class TransitionRepository
{
    /** @var TransitionPersistDriverInterface */
    protected $handler;

    /**
     * TransitionRepository constructor.
     */
    public function __construct()
    {
        $this->handler = app()->make('transition-handler');
    }

    public function persist(TransitionInterface $transition)
    {
        $this->handler->persist($transition, app('time')->now());
    }

    public function find(TransitionQuery $where): Timeline
    {
        return $this->handler->find($where);
    }
}
