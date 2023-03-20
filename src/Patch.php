<?php


namespace Debuqer\EloquentMemory;


use Debuqer\EloquentMemory\PatchTypes\ModelCreated;
use Debuqer\EloquentMemory\PatchTypes\PatchTypeInterface;

class Patch
{
    /**
     * @var PatchTypeInterface
     */
    protected $provider;

    public function __construct($old, $new)
    {
        $this->setProvider($old, $new);
    }

    public function getType(): string
    {
        return $this->provider->getType();
    }

    public function setProvider($old, $new)
    {
        if ( ! $old and $new ) {
            $this->provider = new ModelCreated($new);
        }
    }
}
