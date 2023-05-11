<?php

use Debuqer\EloquentMemory\Tests\Fixtures\PostWithEloquentMemory as Post;
use Debuqer\EloquentMemory\Models\TransitionRepository;
use Debuqer\EloquentMemory\Timeline;

beforeEach(function() {
    $this->post = createAPost(Post::class);
    $this->batchId = app(TransitionRepository::class)->getBatchId();
});

test('it can record model stored', function() {
    /** @var Timeline $timeline */
    $this->timeline = app(TransitionRepository::class)->find(['batch' => $this->batchId]);

    expect( $this->timeline->count())->toBe(1);

    expect( $this->timeline->current()->getTransition()->getType())->toBe('model-created');
    expect( $this->timeline->current()->getTransition()->getModelClass())->toBe(Post::class);
    foreach ( $this->timeline->current()->getTransition()->getAttributes() as $key => $value) {
        expect($value)->toBe($this->post->getRawOriginal($key));
    }
});

test('it can record model updated', function() {
    $oldAttributes = $this->post->getRawOriginal();

    $this->post->update([
        'title' => 'new Title'
    ]);

    /** @var Timeline $timeline */
    $this->timeline = app(TransitionRepository::class)->find(['batch' => $this->batchId]);

    expect($this->timeline->count())->toBe(2);
    $change = $this->timeline->current();

    expect($change->getTransition()->getType())->toBe('model-updated');
    expect($change->getTransition()->getModelClass())->toBe(Post::class);
    expect($change->getTransition()->getModelKey())->toBe($this->post->id);
    expect($change->getTransition()->getOldAttributes())->toBe($oldAttributes);
    expect($change->getTransition()->getAttributes())->toBe($this->post->getRawOriginal());
});
