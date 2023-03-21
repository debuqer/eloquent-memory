<?php


namespace Debuqer\EloquentMemory\ChangeTypes;


interface ChangeTypeInterface
{
    public function getType(): string;
    public function apply();
}
