<?php

namespace Debuqer\EloquentMemory\Tests\Fixtures\DummyTransitionDriver;

use Carbon\Carbon;
use Debuqer\EloquentMemory\Facades\EloquentMemory;
use Debuqer\EloquentMemory\Repositories\TransitionPersistDriver;
use Debuqer\EloquentMemory\Repositories\TransitionQuery;
use Debuqer\EloquentMemory\Timeline;
use Debuqer\EloquentMemory\Transitions\TransitionInterface;

class EloquentTransitionPersistDriver implements TransitionPersistDriver
{
    public static function persist(TransitionInterface $transition, Carbon $dateRecorded): void
    {
        EloquentPersistedTransitionRecord::create([
            'type' => $transition->getType(),
            'address' => $transition->getTransitionStorageAddress(),
            'subject_type' => $transition->getSubjectType(),
            'subject_key' => $transition->getSubjectKey(),
            'properties' => $transition->getProperties(),
            'batch' => EloquentMemory::batchId(),
            'date_recorded' => $dateRecorded->getPreciseTimestamp(),
        ]);
    }

    public static function find(TransitionQuery $where): Timeline
    {
        $timeline = new Timeline();
        EloquentPersistedTransitionRecord::queryOnTransitions($where)->each(function ($item) use (&$timeline) {
            $timeline->insert($item, $item->getCreationDate());
        });

        $timeline->latestFirst();

        return $timeline;
    }
}
