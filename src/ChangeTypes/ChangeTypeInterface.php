<?php


namespace Debuqer\EloquentMemory\ChangeTypes;


interface ChangeTypeInterface
{
    public function getType(): string;
    public function apply();
    public function rollback();
    public function getRollbackChange(): ChangeTypeInterface;
}
