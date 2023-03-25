<?php


namespace Debuqer\EloquentMemory;


use Debuqer\EloquentMemory\ChangeTypes\ModelCreated;
use Debuqer\EloquentMemory\ChangeTypes\ChangeTypeInterface;
use Debuqer\EloquentMemory\ChangeTypes\ModelDeleted;
use Debuqer\EloquentMemory\ChangeTypes\ModelRestored;
use Debuqer\EloquentMemory\ChangeTypes\ModelSoftDeleted;
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
        /** @var ChangeTypeInterface $changeType */
        foreach ($this->getChangeTypes() as $changeType) {
            if ( $changeType::satisfyConditions($old, $new) ) {
                $this->provider = $changeType::create($old, $new);
            }
        }

        if ( ! $this->provider ) {
            throw new UnknownChangeException;
        }
    }

    protected function getChangeTypes(): array
    {
        return [
            ModelUpdated::class,
            ModelCreated::class,
            ModelDeleted::class,
            ModelSoftDeleted::class,
            ModelRestored::class,
        ];
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
