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
    $oldModel = $this->model->getStateOf(Carbon::now()->subMinutes(5)); // return a model when model was created (1 hour ago)
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

it('can re-generate some of destroyed models', function () {
    $this->model->delete();

    $models = [];
    for ($i = 0; $i < 5; $i ++) {
        $models[$i] = $this->createAModelOf(Post::class);
    }

    Carbon::setTestNow(Carbon::now()->addMinutes(10));

    for ($i = 0; $i < 5; $i ++) {
        $models[$i]->delete();
    }

    Carbon::setTestNow(Carbon::now()->addMinutes(10));

    $restoredModels = [];
    for ($i = 0; $i < 5; $i ++) {
        $restoredModels[$i] = $models[$i]->getStateOf(Carbon::now()->subMinutes(20));

        expect($this->arraysAreTheSame($models[$i]->getAttributes(), $restoredModels[$i]->getAttributes()))->toBeTrue();
    }
});

it('can rollback to the old state of model', function () {
    Carbon::setTestNow(Carbon::now()->addMinutes(10));

    $this->model->update([
        'title' => '1 time past'
    ]);

    $state = Post::find(1)->getStateOf(Carbon::now()->subMinutes(2));
    $state->save();

    $model = Post::find(1);

    expect($model->getRawOriginal('title'))->toBe($this->transition['model']->getRawOriginal('title'));
});




