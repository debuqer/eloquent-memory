<?php
use Debuqer\EloquentMemory\ChangeTypes\ModelRestored;
use Debuqer\EloquentMemory\ChangeTypes\ModelSoftDeleted;

beforeEach(function () {
    $softDeletableModelClass = new class extends \Debuqer\EloquentMemory\Tests\Fixtures\Post {
        use \Illuminate\Database\Eloquent\SoftDeletes;

        protected $table = 'posts';

        protected $casts = [
            'meta' => 'json',
        ];
    };

    $attributes = createAFakePost()->getRawOriginal();
    $softDeletableModelClass->setRawAttributes($attributes)->save();
    $before = $softDeletableModelClass::first();
    $before->delete();
    $after = (clone $before);
    $after->restore();

    $this->c = ModelRestored::createFromModel($before, $after);
    $this->before = $before;
    $this->after = $after;
});

test('up will restore a model from database', function () {
    $this->c->up();

    expect($this->after->refresh()->trashed())->toBeFalse();
});

test('getRollbackChange will return instance of ModelSoftDeleted with same properties', function () {
    expect($this->c->getRollbackChange())->toBeInstanceOf(ModelSoftDeleted::class);
    expect($this->c->getRollbackChange()->getModelKey())->toBe($this->c->getModelKey());
    testAttributes($this->c->getRollbackChange()->getOldAttributes(), $this->c->getAttributes());
    testAttributes($this->c->getRollbackChange()->getAttributes(), $this->c->getOldAttributes());
});
