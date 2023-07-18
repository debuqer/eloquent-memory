<?php

namespace Debuqer\EloquentMemory;

use Debuqer\EloquentMemory\Repositories\PersistedTransitionRecordInterface;
use Debuqer\EloquentMemory\Transitions\TransitionInterface;
use Illuminate\Database\Eloquent\Model;

class EloquentMemory
{
    protected $batchId;

    public function __construct()
    {
        $this->batchId = md5(microtime(true));
    }

    public function batchId(): string
    {
        return $this->batchId;
    }

    public function getTransitionFromModel(string $type, Model $model)
    {
        /** @var TransitionInterface $transitionClass */
        $transitionClass = config('eloquent-memory.changes.'.$type);

        return $transitionClass::createFromModel($model);
    }

    public function getTransitionFromPersistedRecord(PersistedTransitionRecordInterface $record)
    {
        return $record->getTransition();
    }
}
