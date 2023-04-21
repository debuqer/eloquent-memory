<?php


namespace Debuqer\EloquentMemory\Tests\Fixtures;

use \Debuqer\EloquentMemory\ChangeTypes\Checkers\AbstractChecker;


class AlwaysTrueChecker extends AbstractChecker
{

    public function condition(): bool
    {
        return true;
    }
}
