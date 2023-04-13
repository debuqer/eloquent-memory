<?php


namespace Debuqer\EloquentMemory\ChangeTypes\Checkers;


abstract class AbstractChecker implements CheckerInterface
{
    protected $notFlag = false;

    protected $item;

    public static function setItem($item)
    {
        return new static($item);
    }

    public function __construct($item)
    {
        $this->item = $item;
    }

    public function not()
    {
        $this->notFlag = ! $this->notFlag;

        return $this;
    }

    public function evaluate(): bool
    {
        $evaluate = $this->condition();

        if ( $this->notFlag ) {
            return ! $evaluate;
        }

        return $evaluate;
    }
}