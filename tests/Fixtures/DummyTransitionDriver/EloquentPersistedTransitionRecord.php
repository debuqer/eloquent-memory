<?php

namespace Debuqer\EloquentMemory\Tests\Fixtures\DummyTransitionDriver;

use Debuqer\EloquentMemory\Repositories\PersistedTransitionRecordInterface;
use Debuqer\EloquentMemory\Repositories\TransitionQuery;
use Debuqer\EloquentMemory\Transitions\TransitionInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class EloquentPersistedTransitionRecord implements PersistedTransitionRecordInterface
{
    public function __construct(protected array $data)
    {

    }

    public function getTransition(): TransitionInterface
    {
        $transitionClass = config('eloquent-memory.changes.' . $this->data['type']);

        return $transitionClass::createFromPersistedRecord($this);
    }

    public static function queryOnTransitions(TransitionQuery $where): Collection
    {
        return collect(EloquentTransitionPersistDriver::getData())->when($where->isSeted('before'), function ($query) use ($where) {
            $query->where('date_recorded', '<', $where->getBefore()->getPreciseTimestamp());
        })->when($where->isSeted('until'), function ($query) use ($where) {
            $query->where('date_recorded', '<=', $where->getUntil()->getPreciseTimestamp());
        })->when($where->isSeted('after'), function ($query) use ($where) {
            $query->where('date_recorded', '>', $where->getAfter()->getPreciseTimestamp());
        })->when($where->isSeted('from'), function ($query) use ($where) {
            $query->where('date_recorded', '>=', $where->getFrom()->getPreciseTimestamp());
        })->when($where->isSeted('take'), function ($query) use ($where) {
            $query->take($where->getTake());
        })->when(!empty($where->getConditions()), function ($query) use($where) {
            foreach ($where->getConditions() as $condition) {
                $query->where(...$condition);
            }
        })->sortBy($where->getOrderKey(), $where->getOrder() == 'desc' ? SORT_DESC : SORT_ASC);
    }

    public function getProperties(): array
    {
        return $this->data['properties'] ?? [];
    }

    public function getSubjectType(): string
    {
        return $this->data['subject_type'];
    }

    public function getSubjectKey(): string
    {
        return $this->data['subject_key'];
    }

    public function getType(): string
    {
        return $this->data['type'];
    }

    public function getCreationDate(): string
    {
        return $this->data['date_recorded'];
    }
}
