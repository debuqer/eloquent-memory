<?php

namespace Debuqer\EloquentMemory\Repositories;

use Debuqer\EloquentMemory\Repositories\Eloquent\EloquentTransitionPersistDriver;
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
        $this->setHandler(config('eloquent-memory.driver', 'eloquent'));
    }

    /**
     * @return $this
     */
    public function driver($driverName): self
    {
        $this->setHandler($driverName);

        return $this;
    }

    protected function setHandler(string $driverName): void
    {
        $this->handler = $this->getPersistDriverHandler($driverName);
    }

    protected function getPersistDriverHandler(string $driverName): EloquentTransitionPersistDriver
    {
        $config = config('eloquent-memory.drivers.'.$driverName);

        return new $config['class_name']();
    }

    public function persist(TransitionInterface $transition)
    {
        $this->handler->persist($transition, app('time')->now());
    }

    public function find(array $where): Timeline
    {
        return $this->handler->find($where);
    }
}
