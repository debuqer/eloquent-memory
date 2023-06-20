<?php
use Carbon\Carbon;
use Debuqer\EloquentMemory\Tests\Fixtures\PostWithRememberState as Post;

beforeEach(function () {
    $this->transition = $this->getTransition('travel-test', Post::class);
    $this->transition['handler']->persist();

    $this->model = (clone $this->transition['model']);
});

it('can return model state for 1 hour ago created', function () {
    Carbon::setTestNow(Carbon::now()->addHour()); // time travel

    $this->model->update([
        'title' => '1 time past'
    ]);

    $this->model->refresh();
    $oldModel = $this->model->getStateOf(Carbon::now()->subMinutes(60)); // return a model when model was created (1 hour ago)

    expect($oldModel->getRawOriginal('title'))->toBe($this->transition['model']->getRawOriginal('title'));
});

it('can return model when it deleted 10 minutes later', function () {
    Carbon::setTestNow(Carbon::now()->addMinutes(60)); // time travel

    $this->model->delete();

    Carbon::setTestNow(Carbon::now()->addMinutes(10));

    $oldModel = $this->model->getStateOf(Carbon::now()->subMinutes(5)); // model already deleted
    expect($oldModel)->toBeNull();

    $oldModel = $this->model->getStateOf(Carbon::now()->subMinutes(5)); // model already deleted
    expect($oldModel)->toBeNull();

    $oldModel = $this->model->getStateOf(Carbon::now()->subMinutes(11)); // model exists
    expect($oldModel->exists)->toBeTrue();
});


