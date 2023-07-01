<?php

namespace Debuqer\EloquentMemory\Repositories;

use Carbon\Carbon;
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
     * @param $driverName
     * @return $this
     */
    public function driver($driverName): self
    {
        $this->setHandler($driverName);

        return $this;
    }

    /**
     * @param string $driverName
     */
    protected function setHandler(string $driverName): void
    {
        $this->handler = $this->getPersistDriverHandler($driverName);
    }

    /**
     * @param string $driverName
     * @return EloquentTransitionPersistDriver
     */
    protected function getPersistDriverHandler(string $driverName): EloquentTransitionPersistDriver
    {
        $config = config('eloquent-memory.drivers.'.$driverName);

        return new $config['class_name']();
    }

    /**
     * @param TransitionInterface $transition
     */
    public function persist(TransitionInterface $transition)
    {
        $this->handler->persist($transition, Carbon::now());
    }

    /**
     * @param array $where
     * @return Timeline
     */
    public function find(array $where): Timeline
    {
        return $this->handler->find($where);
    }
}
