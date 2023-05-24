<?php
use Debuqer\EloquentMemory\Tests\Fixtures\Post;
use Debuqer\EloquentMemory\Tests\Fixtures\User;
use \Debuqer\EloquentMemory\Transitions\ModelCreated;
use \Debuqer\EloquentMemory\Transitions\ModelDeleted;
use Carbon\Carbon;
use Illuminate\Database\QueryException;

beforeEach(function () {
    $class = new Class extends Post {
        public function owner()
        {
            return $this->belongsTo(User::class, 'owner_id');
        }
    };

    // we just need a model not a record in database
    $item = $this->getFilledModelOf($class, Post::class);

    // assume that a transition have created due to our action
    $transition = ModelCreated::createFromModel($item);

    // store for further actions
    $this->transition = $transition;
    $this->item = $item;
    $this->attributes = $item->getRawOriginal();
});

/**
 * @deprecated
 */
test('Transition up will create a model with the same properties', function () {
    $this->transition->up();
    expect($this->item->exists)->toBeTrue(); // item exists

    expect($this->arraysAreTheSame($this->item->getRawOriginal(), $this->attributes))->toBeTrue();
});

/**
 * @deprecated
 */
test('getRollbackChange will return an instanceof ModelDeleted with the same properties ', function () {
    expect($this->transition->getRollbackChange())->toBeInstanceOf(ModelDeleted::class);
    expect($this->transition->getRollbackChange()->getOldAttributes())->toBe($this->item->getRawOriginal());
});

/**
 * @deprecated
 */
test('migrate up will not update created_at and updated_at', function() {
    Carbon::setTestNow(Carbon::now()->addHour()); // traveling in time
    $this->transition->up();
    $post = Post::first(); // get the post directly from database

    expect($post->created_at->toString())->toBe($this->item->created_at->toString());
    expect($post->updated_at->toString())->toBe($this->item->updated_at->toString());
});

/**
 * @deprecated
 */
test('migrate up can not perform creation when model already exists', function() {
    $this->transition->up(); // now model will create after this transition
    $this->transition->up(); // trying to create model again should fail
})->expectException(QueryException::class);


/**
 * @deprecated
 */
test('raise exception when id exists', function() {
    $this->createAPost();

    $this->transition->up();
})->expectException(QueryException::class);

/**
 * @deprecated
 */
test('persist will save a record in db', function () {
    $this->transition->persist();

    expect($this->transition->getModel()->exists)->toBeTrue();
});

/**
 * @deprecated
 */
test('transition can be made by a db record', function() {
    $this->transition->persist(); // let's store the transition for further retrieve
    $newTransition = ModelCreated::createFromPersistedRecord($this->transition->getModel()); // $newTransition must be create


    expect(get_class($newTransition))->toBe(get_class($this->transition));
    expect($newTransition->getProperties())->toBe($this->transition->getProperties());
    expect(get_class($newTransition->getRollbackChange()))->toBe(get_class($this->transition->getRollbackChange()));
    expect($newTransition->getRollbackChange()->getProperties())->toBe($this->transition->getRollbackChange()->getProperties());
});

/**
 * @deprecated
 */
test('created by db record can migrate up and rollback and up again', function() {
    $this->transition->persist(); // let's store the transition for further retrieve

    $newTransition = ModelCreated::createFromPersistedRecord($this->transition->getModel());
    $newTransition->up(); // this will create a model in database
    $this->item->refresh(); // re-query our model
    expect($this->item->exists)->toBeTrue(); // our model must exists

    foreach ($this->item->getRawOriginal() as $attr => $value) {
        expect($value)->toBe((isset($this->attributes[$attr]) ? $this->attributes[$attr] : null));
    }
    $newTransition->down(); // this will destory our model
    expect(Post::find($this->item->id))->toBeNull();

    $newTransition->up(); // this will create our model again

    $newPost = Post::find($this->item->id); // retrieve our model from db
    expect($newPost)->not->toBeNull();
    foreach ($newPost->getRawOriginal() as $attr => $value) {
        expect($value)->toBe((isset($this->attributes[$attr]) ? $this->attributes[$attr] : null));
    }
});

test('mutation doesnt affect on transition properties on persist', function() {
    $mutationClass = new Class extends Post {
        public function getTitleAttribute($value)
        {
            return $value .'::mutated';
        }
    };


    $modelWithMutation = $this->createAModelOf($mutationClass);
    $transition = ModelCreated::createFromModel($modelWithMutation);

    $mutatedTitle = $modelWithMutation->title;
    expect($transition->getAttributes()['title'])->not->toBe($mutatedTitle);
});

test('migrate will fill guarded attributes', function() {
    $this->transition->up(); // this will create our model

    $newModel = Post::first();
    // model stored with id=1
    expect($newModel->getKey())->toBe($this->item->getKey());
});

