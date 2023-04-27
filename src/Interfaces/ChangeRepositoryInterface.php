<?php


namespace Debuqer\EloquentMemory\Interfaces;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Debuqer\EloquentMemory\Timeline;

interface ChangeRepositoryInterface
{
    public function uuid($uuid): self;
    public function batch($batch): self;
    public function model(Model $model): self;
    public function after(Carbon $carbon): self;
    public function before(Carbon $carbon): self;
    public function between(Carbon $before, Carbon $after): self;
    public function where(\Closure $closure): self;

    public function get(): Timeline;
}
