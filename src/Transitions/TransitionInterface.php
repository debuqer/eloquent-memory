<?php


namespace Debuqer\EloquentMemory\Transitions;


use Debuqer\EloquentMemory\Models\ModelTransitionInterface;
use Illuminate\Database\Eloquent\Model;

interface TransitionInterface
{
    public function getProperties(): array;
    public function getType(): string;
    public function up();
    public function down();
    public function getRollbackChange(): TransitionInterface;

    public function persist();
    public function getModel();

    public static function createFromPersistedRecord(ModelTransitionInterface $change);
}
