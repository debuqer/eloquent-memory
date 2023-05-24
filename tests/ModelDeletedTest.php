<?php
use Debuqer\EloquentMemory\Tests\Fixtures\Post;
use Debuqer\EloquentMemory\Transitions\ModelCreated;
use Debuqer\EloquentMemory\Transitions\ModelDeleted;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Debuqer\EloquentMemory\Tests\Fixtures\PostWithSoftDelete;

beforeEach(function () {
    $item = $this->createAPost(); // we already have a post
    $transition = ModelDeleted::createFromModel($item);

    $this->transition = $transition;
    $this->item = $item;
    $this->attributes = $item->getRawOriginal();
});


/**
 * @deprecated
 */
test('up will forceDelete a model from database', function () {
    $this->transition->up();

    Post::findOrFail($this->item->getKey());
})->expectException(ModelNotFoundException::class);

test('up will forceDelete a model from database even when model uses softDelete', function () {
    $softDeletableModel = $this->createAPost(PostWithSoftDelete::class);

    $model = $softDeletableModel;
    $transition = ModelDeleted::createFromModel($model); // must forceDelete the model
    $transition->up();

    $softDeletableModel::withTrashed()->findOrFail($softDeletableModel->getKey());
})->expectException(ModelNotFoundException::class);

test('getRollbackChange returns instance of ModelCreated with same properties ', function () {
    expect($this->transition->getRollbackChange())->toBeInstanceOf(ModelCreated::class);
    expect($this->transition->getRollbackChange()->getAttributes())->toBe($this->item->getRawOriginal());
});

test('migrate down creates a model with the same properties', function() {
    $this->item->forceDelete(); // we assume Post deleted since we need to revert our action
    $this->transition->down(); // must create deleted model

    expect(Post::find($this->item->getKey()))->not->toBeNull();
    foreach ($this->item->getRawOriginal() as $attr => $value) {
        expect($value)->toBe((isset($this->attributes[$attr]) ? $this->attributes[$attr] : null));
    }
});

test('migrate up and migrate down and migrate up again works', function () {
    $this->transition->up();
    expect(Post::find($this->item->getKey()))->toBeNull();
    $this->transition->down();
    expect(Post::find($this->item->getKey()))->not->toBeNull();
    $this->transition->up();
    expect(Post::find($this->item->getKey()))->toBeNull();
});

test('migrate up and migrate up again does not work', function () {
    $this->transition->up();
    $this->transition->up();
})->expectException(ModelNotFoundException::class);

test('migrate down and migrate down again doesnt work', function () {
    $this->item->forceDelete();

    $this->transition->down();
    $this->transition->down();
})->expectException(QueryException::class);

test('migrate up does not work when model already deleted', function () {
    $this->item->forceDelete();

    $this->transition->up();
})->expectException(ModelNotFoundException::class);

test('persist can store transition in db', function () {
    $this->transition->persist();

    expect($this->transition->getModel()->exists)->toBeTrue();
});

test('can be created from persisted model', function () {
    $this->transition->persist();

    $transition = ModelDeleted::createFromPersistedRecord($this->transition->getModel()); // c must be create

    expect(get_class($transition))->toBe(get_class($this->transition));
    expect($transition->getProperties())->toBe($this->transition->getProperties());
    expect(get_class($transition->getRollbackChange()))->toBe(get_class($this->transition->getRollbackChange()));
    expect($transition->getRollbackChange()->getProperties())->toBe($this->transition->getRollbackChange()->getProperties());
});


test('a transition that created by persisted record can be migrate up and down', function () {
    $this->transition->persist();

    $transition = ModelDeleted::createFromPersistedRecord($this->transition->getModel());

    $transition->up();
    expect(Post::first())->toBeNull();
    $transition->down();
    expect(Post::first())->not->toBeNull();
});

test('mutation does not affect on properties on persist', function () {
    $modelClassWithMutation = new Class extends Post {
        protected $table = 'posts';

        public function getTitleAttribute($value)
        {
            return $value .'::mutated';
        }
    };

    Post::query()->delete();
    $modelClassWithMutation->setRawAttributes($this->item->getRawOriginal())->save();
    $modelWithMutation = $modelClassWithMutation::first();
    $change = ModelDeleted::createFromModel($modelClassWithMutation);
    $modelWithMutation->forceDelete();

    expect($change->getOldAttributes()['title'])->not->toBe($modelWithMutation->title);
});
