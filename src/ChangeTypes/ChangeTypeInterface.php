<?php


namespace Debuqer\EloquentMemory\ChangeTypes;


interface ChangeTypeInterface
{
    public static function create($old, $new): self;

    public function getType(): string;
    public function up();
    public function down();
    public function getRollbackChange(): ChangeTypeInterface;
}
