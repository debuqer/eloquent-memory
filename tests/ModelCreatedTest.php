<?php
use Debuqer\EloquentMemory\Tests\Fixtures\Post;
use \Debuqer\EloquentMemory\Transitions\ModelCreated;
use \Debuqer\EloquentMemory\Transitions\ModelDeleted;

beforeEach(function () {
    $item = createAFakePost();
    $transition = ModelCreated::createFromModel($item);

    // change type
    $this->transition = $transition; // change type
    $this->item = $item; // app/Models/Post
    $this->attributes = $item->getRawOriginal(); // faker attributes
});

test('up will create a model with same properties', function () {
    $this->transition->up();
    expect($this->item->exists)->toBeTrue();

    foreach ($this->item->getRawOriginal() as $attr => $value) {
        expect($value)->toBe((isset($this->attributes[$attr]) ? $this->attributes[$attr] : null));
    }
});

test('getRollbackChange will return an instanceof ModelDeleted with same properties ', function () {
    expect($this->transition->getRollbackChange())->toBeInstanceOf(ModelDeleted::class);
    expect($this->transition->getRollbackChange()->getOldAttributes())->toBe($this->item->getRawOriginal());
});

test('migrate up should not update created_at and updated_at', function() {
    \Carbon\Carbon::setTestNow(\Carbon\Carbon::now()->addHour());
    $this->transition->up();
    $newPost = Post::first(); // new post created by migration

    expect($newPost->created_at->toString())->toBe($this->item->created_at->toString());
    expect($newPost->updated_at->toString())->toBe($this->item->updated_at->toString());
});

test('migrate up cant perform creation where model already exists', function() {
    $this->transition->up(); // now model exists in db
    $this->transition->up(); // trying to create model again
})->expectException(\Illuminate\Database\QueryException::class);

test('raise exception when that id exists', function() {
    createAPost();

    $this->transition->up();
})->expectException(\Illuminate\Database\QueryException::class);


test('persist will save a record in db', function () {
    $this->transition->persist();

    expect($this->transition->getModel()->exists)->toBeTrue();
});


test('can be made by a db record', function() {
    $this->transition->persist();

    $newC = ModelCreated::createFromPersistedRecord($this->transition->getModel()); // c must be create

    expect(get_class($newC))->toBe(get_class($this->transition));

    expect($newC->getProperties())->toBe($this->transition->getProperties());
    expect(get_class($newC->getRollbackChange()))->toBe(get_class($this->transition->getRollbackChange()));
    expect($newC->getRollbackChange()->getProperties())->toBe($this->transition->getRollbackChange()->getProperties());
});

test('created by db record can migrate up and rollback and up again', function() {
    $this->transition->persist();

    $newC = ModelCreated::createFromPersistedRecord($this->transition->getModel()); // c must be create
    $newC->up();
    $this->item->refresh();
    expect($this->item->exists)->toBeTrue();
    foreach ($this->item->getRawOriginal() as $attr => $value) {
        expect($value)->toBe((isset($this->attributes[$attr]) ? $this->attributes[$attr] : null));
    }
    $newC->down();
    expect(Post::find($this->item->id))->toBeNull();

    $newC->up();

    $newPost = Post::find($this->item->id);
    expect($newPost)->not->toBeNull();
    foreach ($newPost->getRawOriginal() as $attr => $value) {
        expect($value)->toBe((isset($this->attributes[$attr]) ? $this->attributes[$attr] : null));
    }
});

test('mutation doesnt affect on change persist', function() {
    $modelClassWithMutation = new Class extends Post {
        protected $table = 'posts';

        public function getTitleAttribute($value)
        {
            return $value .'::mutated';
        }
    };

    $modelClassWithMutation->setRawAttributes($this->item->getRawOriginal())->save();
    $modelWithMutation = $modelClassWithMutation::first();
    $change = ModelCreated::createFromModel($modelClassWithMutation);

    expect($change->getAttributes()['title'])->not->toBe($modelWithMutation->title);
});

test('migrate will fill guarded attributes', function() {
    $this->transition->up();

    $newModel = Post::first();
    expect($newModel->getKey())->toBe($this->item->getKey());
});

