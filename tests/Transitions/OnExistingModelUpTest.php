<?php
use Illuminate\Database\QueryException;
use \Debuqer\EloquentMemory\Transitions\ModelCreated;

beforeEach(function () {
    $this->model = $this->createAPost();
    $this->attributes = $this->model->getRawOriginal();

    $this->transitions = [
        'ModelCreated' => ModelCreated::createFromModel($this->model)
    ];
});


it('[ModelCreated] can not re-create the model', function () {
    $this->transitions['ModelCreated']->up();
    $this->transitions['ModelCreated']->up();
})->expectException(QueryException::class);

