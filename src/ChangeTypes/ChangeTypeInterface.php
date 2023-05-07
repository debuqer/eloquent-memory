<?php


namespace Debuqer\EloquentMemory\ChangeTypes;


use Debuqer\EloquentMemory\Change;
use Illuminate\Database\Eloquent\Model;

interface ChangeTypeInterface
{
    public function getParameters();
    public function getType(): string;
    public function up();
    public function down();
    public function getRollbackChange(): ChangeTypeInterface;

    public function persist();
    public function getModel();

    public static function createFromPersistedRecord(Change $change);
    public static function createFromModel(Model $model);
}
