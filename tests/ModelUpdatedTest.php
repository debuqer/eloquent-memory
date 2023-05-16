<?php
use Debuqer\EloquentMemory\Tests\Fixtures\Post;
use Debuqer\EloquentMemory\Transitions\ModelUpdated;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Carbon\Carbon;

beforeEach(function () {
    $before = createAPost();
    $after = (clone $before);
    $after->update([
        'title' => 'Title changed!',
        'json' => ['new json']
    ]);
    $this->before = (clone $before);
    $this->after = (clone $after);

    // reset to state before updating
    $after->delete();
    (new Post())->setRawAttributes($before->getRawOriginal())->save();

    $this->c = ModelUpdated::createFromModel($before, $after);
});

test('up will update a model in database with given attributes', function () {
    $this->c->up();

    expect(Post::first()->title)->toBe($this->after->title);
    expect(Post::first()->json)->toBe($this->after->json);
});

test('getRollbackChange returns instanceof ModelUpdated with reversed properties', function () {
    $after = createAPost();
    $before = (clone $after);
    $before->update([
        'title' => 'Title changed!'
    ]);

    $c = new ModelUpdated([
        'model_class' => get_class($after),
        'key' => $after->getKey(),
        'old' => $before->getRawOriginal(),
        'attributes' => $after->getRawOriginal()
    ]);

    expect($c->getRollbackChange())->toBeInstanceOf(ModelUpdated::class);
    expect($c->getRollbackChange()->getModelKey())->toBe($c->getModelKey());
    testAttributes($c->getRollbackChange()->getOldAttributes(), $c->getAttributes());
    testAttributes($c->getRollbackChange()->getAttributes(), $c->getOldAttributes());
});

test('migrate up and down rollback everything to first place', function () {
    $this->c->up();
    $this->c->down();

    $model = Post::find($this->before->getKey());
    foreach ($this->before->getRawOriginal() as $key => $value) {
        expect($model->getRawOriginal($key))->toBe($value);
    }
});

test('migrate up and down and up again works', function () {
    $this->c->up();
    $this->c->down();
    $this->c->up();

    $model = Post::find($this->after->getKey());
    foreach ($this->after->getRawOriginal() as $key => $value) {
        expect($model->getRawOriginal($key))->toBe($value);
    }
});

test('raise exception when model not exists', function () {
    Post::query()->delete();

    $this->c->up();
})->expectException(ModelNotFoundException::class);


test('doesnt change updated_at when migrate up', function () {
    Carbon::setTestNow(\Carbon\Carbon::now()->addHour());

    $this->c->up();
    $post = Post::first();

    expect($post->created_at->toString())->not->toBe(Carbon::now()->toString());
});

test('migrate up can fill despite guarded attributes', function () {
    /** @var \Illuminate\Database\Eloquent\Model $modelWithGuarded */
    $modelWithGuarded = new Class extends Post {
        protected $table = 'posts';
        protected $guarded = ['id', 'title'];
    };

    $before = $modelWithGuarded->setRawAttributes($this->before->getRawOriginal());
    $after = $modelWithGuarded->setRawAttributes($this->after->getRawOriginal());

    $newC = ModelUpdated::createFromModel($before, $after);
    $newC->up();

    expect($after->title)->toBe($this->after->title);
});

test('migrate up can fill despite hidden attributes', function () {
    /** @var \Illuminate\Database\Eloquent\Model $modelWithGuarded */
    $modelWithGuarded = new Class extends Post {
        protected $table = 'posts';
        protected $hidden = ['title'];
    };

    $before = $modelWithGuarded->setRawAttributes($this->before->getRawOriginal());
    $after = $modelWithGuarded->setRawAttributes($this->after->getRawOriginal());

    $newC = ModelUpdated::createFromModel($before, $after);
    $newC->up();

    expect($after->title)->toBe($this->after->title);
});


test('migrate up can fill despite casted attributes', function () {
    /** @var \Illuminate\Database\Eloquent\Model $modelWithGuarded */
    $modelWithGuarded = new Class extends Post {
        protected $table = 'posts';
        protected $casts = [
            'title' => 'bool'
        ];
    };

    $attributes = createAFakePost()->getRawOriginal();
    $modelWithGuarded->setRawAttributes($attributes)->save();
    $before = Post::latest('id')->first();
    $after = (clone $before);
    $after->update([
        'title' => 'Title changed!'
    ]);

    $newC = ModelUpdated::createFromModel($before, $after);
    $newC->up();

    expect($after->getRawOriginal('title'))->toBe($this->after->getRawOriginal('title'));
});

test('can persist in db', function () {
    $this->c->persist();

    expect($this->c->getModel())->not->toBeNull();
    expect($this->c->getModel()->properties)->toBe($this->c->getProperties());
    expect($this->c->getModel()->properties['old'])->toBe($this->before->getRawOriginal());
    expect($this->c->getModel()->properties['attributes'])->toBe($this->after->getRawOriginal());
    expect($this->c->getModel()->properties['key'])->toBe($this->after->getKey());
    expect($this->c->getModel()->properties['model_class'])->toBe(get_class($this->after));
    expect($this->c->getModel()->type)->toBe('model-updated');
});

test('created from persisted record can migrate up and down', function () {
    $this->c->persist();
    $persist = $this->c->getModel();

    $c = ModelUpdated::createFromPersistedRecord($persist);
    $c->up();
    $post = Post::first();
    expect($post->getRawOriginal('title'))->toBe($this->after->getRawOriginal('title'));
    $c->down();
    $post = Post::first();
    expect($post->getRawOriginal('title'))->toBe($this->before->getRawOriginal('title'));
});

test('raise error when model not exists at all', function () {
    $this->before->forceDelete();

    $this->c->up();
})->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
