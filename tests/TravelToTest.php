<?php
use Carbon\Carbon;
use Debuqer\EloquentMemory\Tests\Fixtures\PostWithEloquentMemory as Post;

beforeEach(function () {
    $this->transition = $this->getTransition('travel-test', Post::class);
    $this->transition['handler']->persist();

    $this->model = (clone $this->transition['model']);
});

it('can retrieve the model after update', function () {
    Carbon::setTestNow(Carbon::now()->addHour()); // time travel

    $this->model->update([
        'title' => '1 time past'
    ]);

    $this->model->refresh();
    $oldModel = $this->model->getStateOf(Carbon::now()->subHour()); // return a model when model was created (1 hour ago)

    expect($oldModel->getRawOriginal('title'))->toBe($this->transition['model']->getRawOriginal('title'));
});

it('can retrieve the model after delete', function () {
    $this->model->forceDelete();
    Carbon::setTestNow(Carbon::now()->addHour()); // time travel

    $oldModel = $this->model->getStateOf(Carbon::now()->subHour()); // return a model when model was created (1 hour ago)
    expect($oldModel)->toBeNull();
});

it('can retrieve the model after soft delete', function () {
    $this->model->delete();
    Carbon::setTestNow(Carbon::now()->addHour()); // time travel

    $oldModel = $this->model->getStateOf(Carbon::now()->subHour()); // return a model when model was created (1 hour ago)
    expect($oldModel)->toBeNull();
});

