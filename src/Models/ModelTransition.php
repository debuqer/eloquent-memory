<?php


namespace Debuqer\EloquentMemory\Models;


use Debuqer\EloquentMemory\Models\Concerns\CanGenerateBatchId;
use Debuqer\EloquentMemory\Transitions\TransitionInterface;
use Illuminate\Database\Eloquent\Model;
use Debuqer\EloquentMemory\Timeline;
use Illuminate\Support\Arr;

class ModelTransition extends Model implements ModelTransitionInterface
{
    use CanGenerateBatchId;

    protected $table = 'model_transitions';

    protected $guarded = ['id'];
    protected $casts = [
        'properties' => 'json'
    ];

    public $timestamps = true;


    public static function persist(TransitionInterface $transition) {
        return static::create([
            'type' => $transition->getType(),
            'properties' => $transition->getProperties(),
            'batch' => app(TransitionRepository::class)->getBatchId()
        ]);
    }

    public static function find(array $where): Timeline
    {
        $timeline = new Timeline();
        static::query()->where($where)->get()->each(function ($item) use(&$timeline) {
            $timeline->insert($item, $item->created_at);
        });

        return $timeline;
    }

    public static function findUsingBatch($batch) {

    }

    public function getTransition(): TransitionInterface
    {
        $transitionClass = config('eloquent-memory.changes.'.$this->type);

        return $transitionClass::createFromPersistedRecord($this);
    }
}
