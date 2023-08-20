<?php

namespace Debuqer\EloquentMemory\Repositories\Eloquent;

use Debuqer\EloquentMemory\Repositories\PersistedTransitionRecordInterface;
use Debuqer\EloquentMemory\Repositories\TransitionQuery;
use Debuqer\EloquentMemory\Transitions\TransitionInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class EloquentPersistedTransitionRecord extends Model implements PersistedTransitionRecordInterface
{
    protected $table = 'model_transitions';

    protected $guarded = ['id'];

    protected $casts = [
        'properties' => 'json',
    ];

    public $timestamps = false;

    public function getTransition(): TransitionInterface
    {
        $transitionClass = config('eloquent-memory.changes.'.$this->type);

        return $transitionClass::createFromPersistedRecord($this);
    }

    public static function queryOnTransitions(TransitionQuery $where): Collection
    {
        return EloquentPersistedTransitionRecord::query()->when($where->isSeted('before'), function ($query) use ($where) {
            $query->where('date_recorded', '<', $where->getBefore()->getPreciseTimestamp());
        })->when($where->isSeted('until'), function ($query) use ($where) {
            $query->where('date_recorded', '<=', $where->getUntil()->getPreciseTimestamp());
        })->when($where->isSeted('after'), function ($query) use ($where) {
            $query->where('date_recorded', '>', $where->getAfter()->getPreciseTimestamp());
        })->when($where->isSeted('from'), function ($query) use ($where) {
            $query->where('date_recorded', '>=', $where->getFrom()->getPreciseTimestamp());
        })->when($where->isSeted('take'), function ($query) use ($where) {
            $query->take($where->getTake());
        })->where($where->getConditions())
            ->orderBy($where->getOrderKey(), $where->getOrder())
            ->get();
    }

    public function getProperties(): array
    {
        return $this->properties ?? [];
    }

    public function getSubjectType(): string
    {
        return $this->subject_type;
    }

    public function getSubjectKey(): string
    {
        return $this->subject_key;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getCreationDate(): string
    {
        return $this->date_recorded;
    }
}
