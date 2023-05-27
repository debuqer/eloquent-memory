<?php
use Illuminate\Database\QueryException;
use Debuqer\EloquentMemory\Tests\Fixtures\Post;
use Debuqer\EloquentMemory\Tests\Fixtures\PostWithSoftDelete;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Debuqer\EloquentMemory\Transitions\ModelSoftDeleted;
use Debuqer\EloquentMemory\Transitions\ModelDeleted;
use Debuqer\EloquentMemory\Transitions\ModelCreated;
use Carbon\Carbon;

it('[ModelCreated] can persist', function () {
    $transition = $this->getTransition('model-created');
    $transition['handler']->persist();

    expect($transition['handler']->getModel())->not->toBeNull();
});

it('[ModelCreated] can be made from persisted record', function () {
    $transition = $this->getTransition('model-created');
    $transition['handler']->persist();

    $persistedTransition = ModelCreated::createFromPersistedRecord($transition['handler']->getModel());

    expect($persistedTransition->getType())->toBe($transition['handler']->getType());
    expect($persistedTransition->getModelClass())->toBe($transition['handler']->getModelClass());
    expect($persistedTransition->getProperties())->toBe($transition['handler']->getProperties());
    expect($persistedTransition->getRollbackChange()->getProperties())->toBe($transition['handler']->getRollbackChange()->getProperties());
});

it('[ModelCreated] can persist without considering mutators', function () {
    $transition = $this->getTransition('model-created');
    $transition['handler']->persist();

    $persistedTransition = ModelCreated::createFromPersistedRecord($transition['handler']->getModel());

    expect($persistedTransition)->not->toBeNull();
    expect($persistedTransition->getAttributes()['title'])->not->toBe('This title has changed');
});

it('[ModelCreated] can be made by persisted record and migrate up, down and up!', function () {
    $transition = $this->getTransition('model-created');
    $transition['handler']->persist();

    $persistedTransition = ModelCreated::createFromPersistedRecord($transition['handler']->getModel());

    $persistedTransition->up();
    expect(Post::find($transition['model']->getKey()))->not->toBeNull();

    $persistedTransition->down();
    expect(Post::find($transition['model']->getKey()))->toBeNull();

    $persistedTransition->up();
    expect(Post::find($transition['model']->getKey()))->not->toBeNull();
    expect($this->arraysAreTheSame(Post::find($transition['model']->getKey())->getRawOriginal(), $transition['model']->getRawOriginal()))->toBeTrue();
    expect($this->arraysAreTheSame(Post::find($transition['model']->getKey())->getAttributes(), $transition['model']->getAttributes()))->toBeTrue();
});

it('[ModelCreated] migrate.up() can not re-create the model', function () {
    $transition = $this->getTransition('model-created');

    $transition['handler']->up();
    $transition['handler']->up();
})->expectException(QueryException::class);

it('[ModelCreated] migrate.down() removes recently created model', function () {
    $transition = $this->getTransition('model-created');
    $transition['handler']->down();

    Post::findOrFail($transition['model']->getKey());
})->expectException(ModelNotFoundException::class);

it('[ModelCreated] has correct rollbackTransition', function () {
    $transition = $this->getTransition('model-created');

    expect($transition['handler']->getRollbackChange())->toBeInstanceOf(ModelDeleted::class);
    expect($transition['handler']->getRollbackChange()->getOldAttributes())->toBe($transition['model']->getRawOriginal());
});


it('[ModelCreated] migrate.up() can re-create the model without changing created_at and updated_at', function () {
    $transition = $this->getTransition('model-created');

    Carbon::setTestNow(Carbon::now()->addHour()); // traveling in time
    $transition['handler']->up();

    $post = Post::first(); // get the post directly from database

    expect($post->created_at->toString())->toBe($transition['model']->created_at->toString());
    expect($post->updated_at->toString())->toBe($transition['model']->updated_at->toString());
});

it('[ModelCreated] migrate.up() can not re-create another model when id reserved', function () {
    $transition = $this->getTransition('model-created');
    $this->createAModelOf(Post::class);

    $transition['handler']->up();
})->expectException(QueryException::class);

it('[ModelCreated] migrate.up() will fill guarded fields too', function () {
    $transition = $this->getTransition('model-created');
    $transition['handler']->up();

    $recentlyReCreatedModel = Post::first();
    expect($recentlyReCreatedModel->getKey())->toBe($transition['model']->getKey());
});

it('[ModelDeleted] can persist', function () {
    $transition = $this->getTransition('model-deleted');
    $transition['handler']->persist();

    expect($transition['handler']->getModel())->not->toBeNull();
});

it('[ModelDeleted] can be made from persisted record', function () {
    $transition = $this->getTransition('model-deleted');
    $transition['handler']->persist();

    $persistedTransition = ModelDeleted::createFromPersistedRecord($transition['handler']->getModel());

    expect($persistedTransition->getType())->toBe($transition['handler']->getType());
    expect($persistedTransition->getModelClass())->toBe($transition['handler']->getModelClass());
    expect($persistedTransition->getProperties())->toBe($transition['handler']->getProperties());
    expect($persistedTransition->getRollbackChange()->getProperties())->toBe($transition['handler']->getRollbackChange()->getProperties());
});

it('[ModelDeleted] retrieved persisted record can migrate.up() and migrate.down()', function () {
    $transition = $this->getTransition('model-deleted');
    $transition['handler']->persist();

    $persistedTransition = ModelDeleted::createFromPersistedRecord($transition['handler']->getModel());

    $persistedTransition->up();
    expect(Post::find($transition['model']->getKey()))->toBeNull();
    $persistedTransition->down();
    expect(Post::find($transition['model']->getKey()))->not->toBeNull();
});

it('[ModelDeleted] can persist without considering mutators', function () {
    $transition = $this->getTransition('model-deleted');
    $transition['handler']->persist();

    $persistedTransition = ModelDeleted::createFromPersistedRecord($transition['handler']->getModel());

    expect($persistedTransition)->not->toBeNull();
    expect($persistedTransition->getOldAttributes()['title'])->not->toBe('This title has changed');
});

it('[ModelDeleted] migrate.up() will forceDelete the model from database', function () {
    $transition = $this->getTransition('model-deleted');
    $transition['handler']->up();

    Post::findOrFail($transition['model']->getKey());
})->expectException(ModelNotFoundException::class);


it('[ModelDeleted] migrate.up(), migrate.down() and migrate.up() works', function () {
    $transition = $this->getTransition('model-deleted');

    $transition['handler']->up();
    expect(Post::find($transition['model']->getKey()))->toBeNull();

    $transition['handler']->down();
    expect(Post::find($transition['model']->getKey()))->not->toBeNull();

    $transition['handler']->up();
    expect(Post::find($transition['model']->getKey()))->toBeNull();
});

it('[ModelDeleted] migrate.up() and migrate.up() doesnt work', function () {
    $transition = $this->getTransition('model-deleted');

    $transition['handler']->up();
    expect(Post::find($transition['model']->getKey()))->toBeNull();

    $transition['handler']->up();
    expect(Post::find($transition['model']->getKey()))->toBeNull();
})->expectException(ModelNotFoundException::class);

it('[ModelDeleted] can delete the model even it uses soft deleting', function () {
    $transition = $this->getTransition('model-deleted', PostWithSoftDelete::class);
    $transition['handler']->up();

    PostWithSoftDelete::withTrashed()->findOrFail($transition['model']->getKey());
})->expectException(ModelNotFoundException::class);

it('[ModelDeleted] can delete already soft deleted model', function () {
    $transition = $this->getTransition('model-deleted', PostWithSoftDelete::class);
    $transition['model']->delete();
    $transition['handler']->up();

    PostWithSoftDelete::withTrashed()->findOrFail($transition['model']->getKey());
})->expectException(ModelNotFoundException::class);

it('[ModelDeleted] migrate.down() can re-create the model', function () {
    $transition = $this->getTransition('model-deleted', Post::class);
    $transition['model']->delete();
    $transition['handler']->down();

    $recentlyReCreatedModel = Post::first();
    expect($recentlyReCreatedModel->getKey())->toBe($transition['model']->getKey());
    expect($this->arraysAreTheSame($recentlyReCreatedModel->getAttributes(), $transition['model']->getAttributes()))->toBeTrue();
    expect($this->arraysAreTheSame($recentlyReCreatedModel->getRawOriginal(), $transition['model']->getRawOriginal()))->toBeTrue();
});

it('[ModelDeleted] migrate.down() and migrate.down() doesnt work', function () {
    $transition = $this->getTransition('model-deleted', Post::class);
    $transition['handler']->down();

    expect(Post::find($transition['model']->getKey()))->not->toBeNull();

    $transition['handler']->down();
    expect(Post::find($transition['model']->getKey()))->not->toBeNull();
})->expectException(QueryException::class);


it('[ModelDeleted] migrate.up() doesnt work when model already deleted', function () {
    $transition = $this->getTransition('model-deleted', Post::class);
    $transition['model']->forceDelete();

    $transition['handler']->up();
    expect(Post::find($transition['model']->getKey()))->not->toBeNull();
})->expectException(ModelNotFoundException::class);

it('[ModelDeleted] has correct rollbackTransition', function () {
    $transition = $this->getTransition('model-deleted', Post::class);

    expect($transition['handler']->getRollbackChange())->toBeInstanceOf(ModelCreated::class);
    expect($transition['handler']->getRollbackChange()->getAttributes())->toBe($transition['model']->getRawOriginal());
});

it('[ModelRestored] can persist', function () {
    $transition = $this->getTransition('model-restored');
    $transition['handler']->persist();

    expect($transition['handler']->getModel())->not->toBeNull();
});

it('[ModelRestored] migrate.up() can restore the model', function () {
    $transition = $this->getTransition('model-restored');
    $transition['handler']->up();

    expect(PostWithSoftDelete::withTrashed()->findOrFail($transition['model']->getKey()))->not->toBeNull();
});

it('[ModelRestored] has correct rollbackTransition', function () {
    $transition = $this->getTransition('model-restored');

    expect($transition['handler']->getRollbackChange())->toBeInstanceOf(ModelSoftDeleted::class);
    expect($transition['handler']->getRollbackChange()->getModelKey())->toBe($transition['model']->getKey());
    expect($transition['handler']->getRollbackChange()->getAttributes())->toBe($transition['model']->getRawOriginal());
});
