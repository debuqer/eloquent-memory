<?php


namespace Debuqer\EloquentMemory\Repositories\Eloquent;


use Debuqer\EloquentMemory\Repositories\PersistedTransitionRecordInterface;
use Debuqer\EloquentMemory\Transitions\TransitionInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Fluent;

class EloquentPersistedTransitionRecord extends Model implements PersistedTransitionRecordInterface
{
    protected $table = 'model_transitions';

    protected $guarded = ['id'];
    protected $casts = [
        'properties' => 'json'
    ];

    public $timestamps = true;

    /**
     * @return TransitionInterface
     */
    public function getTransition(): TransitionInterface
    {
        $transitionClass = config('eloquent-memory.changes.' . $this->type);

        return $transitionClass::createFromPersistedRecord($this);
    }


    /**
     * @param array $data
     * @return Collection
     */
    public static function queryOnTransitions(array $data): Collection
    {
        $where = new Fluent($data);
        return EloquentPersistedTransitionRecord::query()->when($where->offsetExists('before'), function ($query) use ($where) {
            $query->where('created_at', '<', $where->get('before'));
        })->when($where->offsetExists('until'), function ($query) use ($where) {
            $query->where('created_at', '<=', $where->get('until'));
        })->when($where->offsetExists('after'), function ($query) use ($where) {
            $query->where('created_at', '>', $where->get('after'));
        })->when($where->offsetExists('from'), function ($query) use ($where) {
            $query->where('created_at', '>=', $where->get('from'));
        })->when($where->offsetExists('take'), function ($query) use ($where) {
            $query->take($where->get('take'));
        })->where($where->get('conditions', []))
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
}
