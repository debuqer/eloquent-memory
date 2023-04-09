<?php


namespace Debuqer\EloquentMemory\Tests\Fixtures;


class AlwaysTrueChecker extends \Debuqer\EloquentMemory\ChangeTypes\Checkers\AbstractChecker
{

    public function condition(): bool
    {
        return true;
    }
}
