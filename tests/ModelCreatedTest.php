<?php
use Debuqer\EloquentMemory\Tests\Fixtures\Post;
use \Debuqer\EloquentMemory\ChangeTypes\ModelCreated;
use \Debuqer\EloquentMemory\ChangeTypes\ModelDeleted;
use \Illuminate\Database\Eloquent\Factories\Factory;

beforeEach(function () {
    $item = createAFakePost();
    $c = ModelCreated::createFromModel($item);

    // change type
    $this->c = $c; // change type
    $this->item = $item; // app/Models/Post
    $this->attributes = $item->getRawOriginal(); // faker attributes
});

test('ModelCreate::up will create a model with same properties', function () {
    $this->c->up();
    expect($this->item->exists)->toBeTrue();

    foreach ($this->item->getRawOriginal() as $attr => $value) {
        expect($value)->toBe((isset($this->attributes[$attr]) ? $this->attributes[$attr] : null));
    }
});

test('ModelCreated::getRollbackChange will return an instanceof ModelDeleted with same properties ', function () {
    expect($this->c->getRollbackChange())->toBeInstanceOf(ModelDeleted::class);
    expect($this->c->getRollbackChange()->getOldAttributes())->toBe($this->item->getRawOriginal());
});

test('ModelCreate::migrate up should not update created_at and updated_at', function() {
    \Carbon\Carbon::setTestNow(\Carbon\Carbon::now()->addHour());
    $this->c->up();
    $newPost = Post::first(); // new post created by migration

    expect($newPost->created_at->toString())->toBe($this->item->created_at->toString());
    expect($newPost->updated_at->toString())->toBe($this->item->updated_at->toString());
});

test('ModelCreate::migrate up cant perform creation where model already exists', function() {
    $this->c->up(); // now model exists in db
    $this->c->up(); // trying to create model again
})->expectException(\Illuminate\Database\QueryException::class);

test('ModelCreate::raise exception when that id exists', function() {
    createAPost();

    $this->c->up();
})->expectException(\Illuminate\Database\QueryException::class);


test('ModelCreated::persist will save a record in db', function () {
    $this->c->persist();

    expect($this->c->getModel()->exists)->toBeTrue();
});


test('ModelCreate::can be made by a db record', function() {
    $this->c->persist();

    $newC = ModelCreated::createFromPersistedRecord($this->c->getModel()); // c must be create

    expect(get_class($newC))->toBe(get_class($this->c));
    expect($newC->getParameters())->toBe($this->c->getParameters());
    expect(get_class($newC->getRollbackChange()))->toBe(get_class($this->c->getRollbackChange()));
    expect($newC->getRollbackChange()->getParameters())->toBe($this->c->getRollbackChange()->getParameters());
});

test('ModelCreate::created by db record can migrate up and rollback and up again', function() {
    $this->c->persist();

    $newC = ModelCreated::createFromPersistedRecord($this->c->getModel()); // c must be create
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
