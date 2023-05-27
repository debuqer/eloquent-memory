<?php
use Debuqer\EloquentMemory\Tests\Fixtures\Post;
use Debuqer\EloquentMemory\Transitions\ModelUpdated;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Carbon\Carbon;

beforeEach(function () {
    $before = $this->createAPost();
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

    $attributes = $this->createAFakePost()->getRawOriginal();
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
