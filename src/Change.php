<?php


namespace Debuqer\EloquentMemory;


use Debuqer\EloquentMemory\ChangeTypes\ModelCreated;
use Debuqer\EloquentMemory\ChangeTypes\ChangeTypeInterface;
use Debuqer\EloquentMemory\ChangeTypes\ModelDeleted;
use Debuqer\EloquentMemory\ChangeTypes\ModelUpdated;
use Debuqer\EloquentMemory\Exceptions\UnknownChangeException;
use Debuqer\EloquentMemory\Tests\Example\ExampleModel;

class Change
{
    /**
     * @var ChangeTypeInterface
     */
    protected $provider;

    public function __construct($old, $new)
    {
        $this->setProvider($old, $new);
    }

    public function getProvider(): ChangeTypeInterface
    {
        return $this->provider;
    }

    public function getType(): string
    {
        return $this->getProvider()->getType();
    }

    public function setProvider($old, $new)
    {
        if ( ! $old and $new ) {
            $this->provider = new ModelCreated($new);
        } else if ( $old and ! $new ) {
            $this->provider = new ModelDeleted($old);
        } else if ( count(array_merge(array_diff($old->getAttributes(), $new->getAttributes()), array_diff($new->getAttributes(), $old->getAttributes()))) > 0) {
            $this->provider = new ModelUpdated($old, $new);
        }

        else {
            throw new UnknownChangeException;
        }
    }

    public function apply()
    {
        $this->provider->apply();
    }

    public function rollback()
    {
        $this->provider->rollback();
    }
}
