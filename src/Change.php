<?php


namespace Debuqer\EloquentMemory;


use Debuqer\EloquentMemory\PatchTypes\ModelCreated;
use Debuqer\EloquentMemory\PatchTypes\ChangeTypeInterface;
use Illuminate\Support\Facades\DB;

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
        }
    }
}
