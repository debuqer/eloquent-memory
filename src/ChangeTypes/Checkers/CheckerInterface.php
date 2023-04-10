<?php


namespace Debuqer\EloquentMemory\ChangeTypes\Checkers;


interface CheckerInterface
{
    public function __construct($item);

    public function condition(): bool;
    public function evaluate(): bool;
}
