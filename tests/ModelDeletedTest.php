<?php
use Debuqer\EloquentMemory\Tests\Fixtures\Post;
use Debuqer\EloquentMemory\ChangeTypes\ModelCreated;
use Debuqer\EloquentMemory\ChangeTypes\ModelDeleted;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;

beforeEach(function () {
    $item = createAPost();
    $c = ModelDeleted::createFromModel($item);

    // change type
    $this->c = $c; // change type
    $this->item = $item; // app/Models/Post
    $this->attributes = $item->getRawOriginal(); // faker attributes
});


test('up will forceDelete a model from database', function () {
    $this->c->up();

    expect(Post::find($this->item->getKey()))->toBeNull();
});

test('up will forceDelete a model from database when model has softDelete', function () {
    $softDeletableModelClass = new class extends \Debuqer\EloquentMemory\Tests\Fixtures\Post {
        use \Illuminate\Database\Eloquent\SoftDeletes;

        protected $table = 'posts';
    };

    $attributes = createAFakePost()->getRawOriginal();
    $softDeletableModelClass->setRawAttributes($attributes)->save();

    $model = $softDeletableModelClass::first();
    $c = ModelDeleted::createFromModel($model);
    $c->up();

    expect($softDeletableModelClass::withTrashed()->find($this->item->getKey()))->toBeNull();
});

test('getRollbackChange returns instance of ModelCreated with same properties ', function () {
    expect($this->c->getRollbackChange())->toBeInstanceOf(ModelCreated::class);
    expect($this->c->getRollbackChange()->getAttributes())->toBe($this->item->getRawOriginal());
});

test('migrate down create the model with same properties', function() {
    $this->item->forceDelete();
    $this->c->down();

    expect(Post::find($this->item->getKey()))->not->toBeNull();
    foreach ($this->item->getRawOriginal() as $attr => $value) {
        expect($value)->toBe((isset($this->attributes[$attr]) ? $this->attributes[$attr] : null));
    }
});

test('migrate up and migrate down and migrate up again works', function () {
    $this->c->up();
    expect(Post::find($this->item->getKey()))->toBeNull();
    $this->c->down();
    expect(Post::find($this->item->getKey()))->not->toBeNull();
    $this->c->up();
    expect(Post::find($this->item->getKey()))->toBeNull();
});

test('migrate up and migrate up again doesnt work', function () {
    $this->c->up();
    $this->c->up();
})->expectException(ModelNotFoundException::class);

test('migrate down and migrate down again doesnt work', function () {
    $this->item->forceDelete();

    $this->c->down();
    $this->c->down();
})->expectException(QueryException::class);

test('migrate up doesnt work when model already deleted', function () {
    $this->item->forceDelete();

    $this->c->up();
})->expectException(ModelNotFoundException::class);

test('persist can store change in db', function () {
    $this->c->persist();

    expect($this->c->getModel()->exists)->toBeTrue();
});

test('can be created from persisted model', function () {
    $this->c->persist();

    $newC = ModelDeleted::createFromPersistedRecord($this->c->getModel()); // c must be create

    expect(get_class($newC))->toBe(get_class($this->c));
    expect($newC->getParameters())->toBe($this->c->getParameters());
    expect(get_class($newC->getRollbackChange()))->toBe(get_class($this->c->getRollbackChange()));
    expect($newC->getRollbackChange()->getParameters())->toBe($this->c->getRollbackChange()->getParameters());
});


test('created by persisted record can be migrate up and down', function () {
    $this->c->persist();

    $newC = ModelDeleted::createFromPersistedRecord($this->c->getModel()); // c must be create

    $newC->up();
    expect(Post::first())->toBeNull();
    $newC->down();
    expect(Post::first())->not->toBeNull();
});

test('mutation doesnt affect on data', function () {
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
