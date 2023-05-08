<?php
use Debuqer\EloquentMemory\ChangeTypes\ModelSoftDeleted;
use Debuqer\EloquentMemory\ChangeTypes\ModelRestored;
use Debuqer\EloquentMemory\Tests\Fixtures\User;

beforeEach(function () {
    $softDeletableModelClass = new class extends \Debuqer\EloquentMemory\Tests\Fixtures\Post
    {
        use \Illuminate\Database\Eloquent\SoftDeletes;

        protected $table = 'posts';

        protected $casts = [
            'meta' => 'json',
        ];
    };

    $attributes = createAFakePost()->getRawOriginal();
    $softDeletableModelClass->setRawAttributes($attributes)->save();
    $before = $softDeletableModelClass::first();
    $after = (clone $before);
    $after->delete();

    $this->c = ModelSoftDeleted::createFromModel($before, $after);
    $after->restore();

    $this->before = $before;
    $this->after = $after;
});

/**
 * ModelSoftDeleted
 */
test('ModelSoftDeleted::up will soft delete a model from database', function () {
    $this->c->up();

    expect($this->after->refresh()->trashed())->toBeTrue();
});


/**
 * ModelSoftDeleted Rollback
 */
test('ModelSoftDeleted::getRollbackChange will return instance of ModelRestored with same properties', function () {
    expect($this->c->getRollbackChange())->toBeInstanceOf(ModelRestored::class);
    expect($this->c->getRollbackChange()->getModelKey())->toBe($this->c->getModelKey());
    testAttributes($this->c->getRollbackChange()->getOldAttributes(), $this->c->getAttributes());
    testAttributes($this->c->getRollbackChange()->getAttributes(), $this->c->getOldAttributes());
});

