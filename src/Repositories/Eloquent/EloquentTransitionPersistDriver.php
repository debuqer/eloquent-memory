<?php

namespace Debuqer\EloquentMemory\Repositories\Eloquent;

use Carbon\Carbon;
use Debuqer\EloquentMemory\Facades\EloquentMemory;
use Debuqer\EloquentMemory\Repositories\TransitionPersistDriverInterface;
use Debuqer\EloquentMemory\Transitions\TransitionInterface;
use Debuqer\EloquentMemory\Timeline;

class EloquentTransitionPersistDriver implements TransitionPersistDriverInterface
{
    /**
     * @param TransitionInterface $transition
     * @param Carbon $dateRecorded
     */
    public static function persist(TransitionInterface $transition, Carbon $dateRecorded): void
    {
        echo $dateRecorded->getTimestampMs().PHP_EOL;
        EloquentPersistedTransitionRecord::create([
            'type' => $transition->getType(),
            'address' => $transition->getTransitionStorageAddress(),
            'subject_type' => $transition->getSubjectType(),
            'subject_key' => $transition->getSubjectKey(),
            'properties' => $transition->getProperties(),
            'batch' => EloquentMemory::batchId(),
            'date_recorded' => $dateRecorded->getTimestampMs(),
        ]);
    }

    /**
     * @param array $where
     * @return Timeline
     */
    public static function find(array $where): Timeline
    {
        $timeline = new Timeline();
        EloquentPersistedTransitionRecord::queryOnTransitions($where)->each(function ($item) use (&$timeline) {
            $timeline->insert($item, $item->getCreationDate());
        });

        $timeline->latestFirst();

        return $timeline;
    }
}
