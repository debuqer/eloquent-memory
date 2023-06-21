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
     * @var string
     */
    protected $driver;

    public function __construct()
    {
        $this->driver = config('eloquent-memory.drivers.default', 'eloquent');

        $this->setHandler();
    }


    /**
     * @param $driverName
     * @return $this
     */
    public function driver($driverName): self
    {
        $this->driver = $driverName;
        $this->setHandler();

        return $this;
    }

    protected function setHandler(): void
    {
        $this->handler = $this->getPersistDriverHandler();
    }

    /**
     * @param $handler
     * @return EloquentTransitionPersistDriver
     */
    protected function getPersistDriverHandler(): EloquentTransitionPersistDriver
    {
        $config = config('eloquent-memory.drivers.'.$this->driver);

        return new $config['class_name']();
    }

    public function persist(TransitionInterface $transition)
    {
        $this->handler->persist($transition);
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
