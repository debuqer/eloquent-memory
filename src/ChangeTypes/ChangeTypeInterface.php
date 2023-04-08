<?php


namespace Debuqer\EloquentMemory\ChangeTypes;


interface ChangeTypeInterface
{
    public static function isApplicable($old, $new): bool;
    public static function create($old, $new): self;

    public function getType(): string;
    public function apply();
    public function rollback();
    public function getRollbackChange(): ChangeTypeInterface;
}
