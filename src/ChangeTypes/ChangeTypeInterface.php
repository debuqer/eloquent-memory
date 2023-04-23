<?php


namespace Debuqer\EloquentMemory\ChangeTypes;


interface ChangeTypeInterface
{
    public function getType(): string;
    public function up();
    public function down();
    public function getRollbackChange(): ChangeTypeInterface;
}
