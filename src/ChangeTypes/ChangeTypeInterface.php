<?php


namespace Debuqer\EloquentMemory\ChangeTypes;


interface ChangeTypeInterface
{
    public function getParameters();
    public function getType(): string;
    public function up();
    public function down();
    public function getRollbackChange(): ChangeTypeInterface;

    public function persist();
    public function getModel();
}
