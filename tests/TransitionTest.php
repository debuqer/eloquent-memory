<?php
use Illuminate\Database\QueryException;
use Debuqer\EloquentMemory\Tests\Fixtures\Post;
use Debuqer\EloquentMemory\Tests\Fixtures\PostWithSoftDelete;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Debuqer\EloquentMemory\Transitions\ModelSoftDeleted;
use Debuqer\EloquentMemory\Transitions\ModelDeleted;
use Debuqer\EloquentMemory\Transitions\ModelCreated;
use Debuqer\EloquentMemory\Transitions\ModelRestored;
use Debuqer\EloquentMemory\Transitions\ModelUpdated;
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

it('[ModelRestored] can persist in db', function () {
    $transition = $this->getTransition('model-restored');
    $transition['handler']->persist();

    expect($transition['handler']->getModel())->not->toBeNull();
    expect($transition['handler']->getModel()->properties)->toBe($transition['handler']->getProperties());
    expect($transition['handler']->getModel()->properties['old'])->toBe($transition['model']->getRawOriginal());
    expect($transition['handler']->getModel()->properties['attributes'])->toBe($transition['after']->getRawOriginal());
    expect($transition['handler']->getModel()->properties['key'])->toBe($transition['after']->getKey());
    expect($transition['handler']->getModel()->properties['model_class'])->toBe(get_class($transition['after']));
    expect($transition['handler']->getModel()->type)->toBe('model-restored');
});

it('[ModelRestored] migrate.up() can restore the model', function () {
    $transition = $this->getTransition('model-restored');
    $transition['handler']->up();

    expect(PostWithSoftDelete::withTrashed()->findOrFail($transition['model']->getKey()))->not->toBeNull();
});

it('[ModelRestored] migrate.up() raises error when model not exists', function () {
    $transition = $this->getTransition('model-restored');
    $transition['model']->forceDelete();

    $transition['handler']->up();
})->expectException(ModelNotFoundException::class);


it('[ModelRestored] which created from persisted record can migrate.up() and migrate.down()', function () {
    $transition = $this->getTransition('model-restored');
    $transition['handler']->persist();
    $persistedTransition = ModelRestored::createFromPersistedRecord($transition['handler']->getModel());

    $persistedTransition->up();
    $post = Post::first();
    expect($post->getRawOriginal('deleted_at'))->toBe($transition['after']->getRawOriginal('deleted_at'));

    $persistedTransition->down();
    $post = Post::first();
    expect($post->getRawOriginal('deleted_at'))->toBe($transition['model']->getRawOriginal('deleted_at'));
});

it('[ModelRestored] has correct rollbackTransition', function () {
    $transition = $this->getTransition('model-restored');

    expect($transition['handler']->getRollbackChange())->toBeInstanceOf(ModelSoftDeleted::class);
    expect($transition['handler']->getRollbackChange()->getModelKey())->toBe($transition['model']->getKey());
    expect($transition['handler']->getRollbackChange()->getAttributes())->toBe($transition['model']->getRawOriginal());
});


it('[ModelSoftDeleted] can persist in db', function () {
    $transition = $this->getTransition('model-soft-deleted');
    $transition['handler']->persist();

    expect($transition['handler']->getModel())->not->toBeNull();
    expect($transition['handler']->getModel()->properties)->toBe($transition['handler']->getProperties());
    expect($transition['handler']->getModel()->properties['old'])->toBe($transition['model']->getRawOriginal());
    expect($transition['handler']->getModel()->properties['attributes'])->toBe($transition['after']->getRawOriginal());
    expect($transition['handler']->getModel()->properties['key'])->toBe($transition['after']->getKey());
    expect($transition['handler']->getModel()->properties['model_class'])->toBe(get_class($transition['after']));
    expect($transition['handler']->getModel()->type)->toBe('model-soft-deleted');
});

it('[ModelSoftDeleted] migrate.up() can delete the model', function () {
    $transition = $this->getTransition('model-soft-deleted');
    $transition['handler']->up();

    expect(PostWithSoftDelete::findOrFail($transition['model']->getKey()));
})->expectException(ModelNotFoundException::class);

it('[ModelSoftDeleted] migrate.up() raises error when model not exists', function () {
    $transition = $this->getTransition('model-soft-deleted');
    $transition['model']->forceDelete();

    $transition['handler']->up();
})->expectException(ModelNotFoundException::class);


it('[ModelSoftDeleted] which created from persisted record can migrate.up() and migrate.down()', function () {
    $transition = $this->getTransition('model-soft-deleted');
    $transition['handler']->persist();
    $persistedTransition = ModelSoftDeleted::createFromPersistedRecord($transition['handler']->getModel());

    $persistedTransition->up();
    $post = Post::first();
    expect($post->getRawOriginal('deleted_at'))->toBe($transition['after']->getRawOriginal('deleted_at'));

    $persistedTransition->down();
    $post = Post::first();
    expect($post->getRawOriginal('deleted_at'))->toBe($transition['model']->getRawOriginal('deleted_at'));
});

it('[ModelSoftDeleted] has correct rollbackTransition', function () {
    $transition = $this->getTransition('model-soft-deleted');

    expect($transition['handler']->getRollbackChange())->toBeInstanceOf(ModelRestored::class);
    expect($transition['handler']->getRollbackChange()->getModelKey())->toBe($transition['model']->getKey());
    expect($transition['handler']->getRollbackChange()->getAttributes())->toBe($transition['model']->getRawOriginal());
});

it('[ModelSoftDeleted] raise error when model not uses softDelete', function () {
    $transition = $this->getTransition('model-soft-deleted', Post::class);

    $transition['handler']->up();
})->expectException(BadMethodCallException::class);

it('[ModelSoftDeleted] migrate.up() will only soft delete', function () {
    $transition = $this->getTransition('model-soft-deleted');

    $transition['handler']->up();

    expect($transition['model']->refresh()->trashed())->toBeTrue();
});


it('[ModelUpdated] can persist', function () {
    $transition = $this->getTransition('model-updated');
    $transition['handler']->up();
    $transition['handler']->persist();

    expect($transition['handler']->getModel())->not->toBeNull();
    expect($transition['handler']->getModel()->properties)->toBe($transition['handler']->getProperties());
    expect($transition['handler']->getModel()->properties['old'])->toBe($transition['model']->getRawOriginal());
    expect($transition['handler']->getModel()->properties['attributes'])->toBe($transition['after']->getRawOriginal());
    expect($transition['handler']->getModel()->properties['key'])->toBe($transition['after']->getKey());
    expect($transition['handler']->getModel()->properties['model_class'])->toBe(get_class($transition['after']));
    expect($transition['handler']->getModel()->type)->toBe('model-updated');
});


it('[ModelUpdated] which created from persisted record can migrate.up() and migrate.down()', function () {
    $transition = $this->getTransition('model-updated');
    $transition['handler']->persist();

    $persistedTransition = ModelUpdated::createFromPersistedRecord($transition['handler']->getModel());

    $persistedTransition->up();
    $post = Post::first();
    expect($post->getRawOriginal('title'))->toBe($transition['after']->getRawOriginal('title'));

    $persistedTransition->down();
    $post = Post::first();
    expect($post->getRawOriginal('title'))->toBe($transition['model']->getRawOriginal('title'));
});

it('[ModelUpdated] migrate.up() updates the model', function () {
    $transition = $this->getTransition('model-updated');
    $transition['handler']->up();

    $post = Post::find($transition['model']->getKey());
    expect($post->getRawOriginal('title'))->toBe($transition['after']->getRawOriginal('title'));
    expect($post->getRawOriginal('json'))->toBe($transition['after']->getRawOriginal('json'));
    expect($post->title)->toBe($transition['after']->refresh()->title);
    expect($post->json)->toBe($transition['after']->refresh()->json);
});

it('[ModelUpdated] has correct rollbackTransition', function () {
    $transition = $this->getTransition('model-updated');
    $transition['handler']->up();


    expect($transition['handler']->getRollbackChange())->toBeInstanceOf(ModelUpdated::class);
    expect($transition['handler']->getRollbackChange()->getModelKey())->toBe($transition['handler']->getModelKey());
    expect($this->arraysAreTheSame($transition['handler']->getRollbackChange()->getOldAttributes(), $transition['handler']->getAttributes()))->toBeTrue();
    expect($this->arraysAreTheSame($transition['handler']->getRollbackChange()->getAttributes(), $transition['handler']->getOldAttributes()))->toBeTrue();
});

it('[ModelUpdated] migrate.up() and migrate.down() rollback everything to the first place', function () {
    $transition = $this->getTransition('model-updated');

    $transition['handler']->up();
    $transition['handler']->down();

    expect($this->arraysAreTheSame($transition['handler']->getRollbackChange()->getAttributes(), $transition['handler']->getOldAttributes()))->toBeTrue();
});

it('[ModelUpdated] migrate.up() and migrate.down() and migrate.up() works', function () {
    $transition = $this->getTransition('model-updated');

    $transition['handler']->up();
    $transition['handler']->down();
    $transition['handler']->up();

    expect($this->arraysAreTheSame($transition['handler']->getRollbackChange()->getAttributes(), $transition['handler']->getAttributes()))->toBeTrue();
});

it('[ModelUpdated] migrate.up() raises error when model not exists', function () {
    $transition = $this->getTransition('model-updated');
    $transition['model']->forceDelete();

    $transition['handler']->up();
})->expectException(ModelNotFoundException::class);

it('[ModelUpdated] migrate.up() doesnt change updated_at when migrate up', function () {
    $now = Carbon::now();
    $transition = $this->getTransition('model-updated');

    Carbon::setTestNow($now->addHour());
    $transition['handler']->up();

    $post = Post::first();
    expect($post->created_at->format('H:i'))->not->toBe($now->format('H:i'));
});

it('[ModelUpdated] migrate.up() can fill despite of guarded attributes', function () {
    /** @var \Illuminate\Database\Eloquent\Model $modelWithGuarded */
    $modelWithGuarded = new Class extends Post {
        protected $table = 'posts';
        protected $guarded = ['id', 'title'];
    };

    $transition = $this->getTransition('model-updated', $modelWithGuarded);
    $transition['handler']->up();

    $post = Post::first();
    expect($post->title)->toBe($transition['model']->title);
});


it('[ModelUpdated] migrate.up() can fill despite of hidden attributes', function () {
    /** @var \Illuminate\Database\Eloquent\Model $modelWithHidden */
    $modelWithHidden = new Class extends Post {
        protected $table = 'posts';
        protected $hidden = ['title'];
    };

    $transition = $this->getTransition('model-updated', $modelWithHidden);
    $transition['handler']->up();

    $post = Post::first();
    expect($post->title)->toBe($transition['model']->title);
});

it('[ModelUpdated] migrate.up() can fill despite of casted values', function () {
    /** @var \Illuminate\Database\Eloquent\Model $modelWithCasts */
    $modelWithCasts = new Class extends Post {
        protected $table = 'posts';
        protected $casts = [
            'title' => 'bool',
            'meta' => 'json'
        ];
    };

    $transition = $this->getTransition('model-updated', $modelWithCasts);
    $transition['handler']->up();

    $post = Post::first();
    expect($post->title)->toBe($transition['model']->title);
});

it('[ModelUpdated] raise error when model not exists at all', function () {
    $transition = $this->getTransition('model-updated');
    $transition['model']->forceDelete();

    $transition['handler']->up();
})->expectException(ModelNotFoundException::class);
