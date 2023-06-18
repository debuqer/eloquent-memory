<?php


namespace Debuqer\EloquentMemory\StorageModels;


use Debuqer\EloquentMemory\Facades\EloquentMemory;
use Debuqer\EloquentMemory\Transitions\TransitionInterface;
use Illuminate\Database\Eloquent\Model;
use Debuqer\EloquentMemory\Timeline;
use Illuminate\Support\Fluent;

class ModelTransition extends Model implements TransitionRepositoryInterface
{
    protected $table = 'model_transitions';

    protected $guarded = ['id'];
    protected $casts = [
        'properties' => 'json'
    ];

    public $timestamps = true;


    public static function persist(TransitionInterface $transition) {
        return static::create([
            'type' => $transition->getType(),
            'address' => $transition->getTransitionStorageAddress(),
            'subject_type' => $transition->getSubjectType(),
            'subject_key' => $transition->getSubjectKey(),
            'properties' => $transition->getProperties(),
            'batch' => EloquentMemory::batchId(),
        ]);
    }


    public static function queryOnTransitions(array $data)
    {
        $where = new Fluent($data);
        return static::query()->when($where->offsetExists('before'), function ($query) use($where) {
            $query->where('created_at', '<', $where->get('before'));
        })->when($where->offsetExists('until'), function ($query) use($where) {
            $query->where('created_at', '<=', $where->get('until'));
        })->when($where->offsetExists('after'), function ($query) use($where) {
            $query->where('created_at', '>', $where->get('after'));
        })->when($where->offsetExists('to'), function ($query) use($where) {
            $query->where('created_at', '>=', $where->get('to'));
        })->when($where->offsetExists('take'), function ($query) use($where) {
            $query->take($where->get('take'));
        })->where($where->get('conditions', []));
    }

    public static function find(array $where): Timeline
    {
        $timeline = new Timeline();
        static::queryOnTransitions($where)->get()->each(function ($item) use(&$timeline) {
                $timeline->insert($item, $item->id);
            });

        return $timeline;
    }

    public function getTransition(): TransitionInterface
    {
        $transitionClass = config('eloquent-memory.changes.'.$this->type);

        return $transitionClass::createFromPersistedRecord($this);
    }
}
