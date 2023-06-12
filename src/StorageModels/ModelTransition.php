<?php


namespace Debuqer\EloquentMemory\StorageModels;


use Carbon\Carbon;
use Debuqer\EloquentMemory\StorageModels\Concerns\CanGenerateBatchId;
use Debuqer\EloquentMemory\Transitions\TransitionInterface;
use Illuminate\Database\Eloquent\Model;
use Debuqer\EloquentMemory\Timeline;
use Illuminate\Support\Arr;

class ModelTransition extends Model implements TransitionStorageModelContract
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
            'address' => $transition->getTransitionStorageAddress(),
            'type' => $transition->getType(),
            'model_class' => $transition->getModelClass(),
            'properties' => $transition->getProperties(),
            'batch' => app(TransitionRepository::class)->getBatchId()
        ]);
    }

    public static function find(array $where, int $limit = null, Carbon $until = null, Carbon $from = null): Timeline
    {
        $timeline = new Timeline();
        static::query()->where($where)
            ->when($until, function ($query) use($until) {
                $query->where('created_at', '<=', $until);
            })->when($from, function ($query) use($from) {
                $query->where('created_at', '>=', $from);
            })
            ->when($limit, function ($query) use($limit) {
                $query->limit($limit);
            })->get()->each(function ($item) use(&$timeline) {
                $timeline->insert($item, $item->id);
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
