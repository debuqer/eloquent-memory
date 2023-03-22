<?php


namespace Debuqer\EloquentMemory\ChangeTypes;


interface ChangeTypeInterface
{
    public function getType(): string;
    public static function satisfyConditions($old, $new): bool;
    public static function create($old, $new): self ;
    public function apply();
    public function rollback();
    public function getRollbackChange(): ChangeTypeInterface;
}
