<?php


namespace Debuqer\EloquentMemory\Repositories\Eloquent;


use Debuqer\EloquentMemory\Repositories\ModelInterface;
use Debuqer\EloquentMemory\Transitions\TransitionInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Fluent;

class EloquentModel extends Model implements ModelInterface
{
    protected $table = 'model_transitions';

    protected $guarded = ['id'];
    protected $casts = [
        'properties' => 'json'
    ];

    public $timestamps = true;

    public function getTransition(): TransitionInterface
    {
        $transitionClass = config('eloquent-memory.changes.'.$this->type);

        return $transitionClass::createFromPersistedRecord($this);
    }


    public static function queryOnTransitions(array $data)
    {
        $where = new Fluent($data);
        return EloquentModel::query()->when($where->offsetExists('before'), function ($query) use($where) {
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
}
