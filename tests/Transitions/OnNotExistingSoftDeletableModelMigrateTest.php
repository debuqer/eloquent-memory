<?php
use Debuqer\EloquentMemory\Tests\Fixtures\PostWithSoftDelete;
use \Debuqer\EloquentMemory\Transitions\ModelCreated;

beforeEach(function () {
    $this->model = $this->createAFakePost(PostWithSoftDelete::class);
    $this->attributes = $this->model->getRawOriginal();

    $this->transitions = [
        'ModelCreated' => ModelCreated::createFromModel($this->model)
    ];
});
