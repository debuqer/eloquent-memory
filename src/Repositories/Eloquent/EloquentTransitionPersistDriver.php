<?php


namespace Debuqer\EloquentMemory\Repositories\Eloquent;


use Debuqer\EloquentMemory\Facades\EloquentMemory;
use Debuqer\EloquentMemory\Repositories\TransitionPersistDriverInterface;
use Debuqer\EloquentMemory\Transitions\TransitionInterface;
use Debuqer\EloquentMemory\Timeline;
use Illuminate\Support\Fluent;

class EloquentTransitionPersistDriver implements TransitionPersistDriverInterface
{
    public static function persist(TransitionInterface $transition) {
        return EloquentPersistedTransactionRecord::create([
            'type' => $transition->getType(),
            'address' => $transition->getTransitionStorageAddress(),
            'subject_type' => $transition->getSubjectType(),
            'subject_key' => $transition->getSubjectKey(),
            'properties' => $transition->getProperties(),
            'batch' => EloquentMemory::batchId(),
        ]);
    }

    public static function find(array $where): Timeline
    {
        $timeline = new Timeline();
        EloquentPersistedTransactionRecord::queryOnTransitions($where)->get()->each(function ($item) use (&$timeline) {
            $timeline->insert($item, $item->id);
        });

        return $timeline;
    }
}
