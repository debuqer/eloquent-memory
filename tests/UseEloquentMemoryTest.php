<?php

use Debuqer\EloquentMemory\Tests\Fixtures\PostWithEloquentMemory as Post;
use Debuqer\EloquentMemory\Tests\Fixtures\SoftDeletedPostWithEloquentMemory as SoftDeletedPost;
use Debuqer\EloquentMemory\Models\TransitionRepository;
use Debuqer\EloquentMemory\Timeline;
use Debuqer\EloquentMemory\Tests\Fixtures\SoftDeletedPostWithEloquentMemory;

beforeEach(function() {
    $this->post = $this->createAPost(Post::class);
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

test('it can record model deleted', function() {
    $oldAttributes = $this->post->getRawOriginal();

    $this->post->delete();
    /** @var Timeline $timeline */
    $this->timeline = app(TransitionRepository::class)->find(['batch' => $this->batchId]);

    expect($this->timeline->count())->toBe(2);
    $change = $this->timeline->current();

    expect($change->getTransition()->getType())->toBe('model-deleted');
    expect($change->getTransition()->getModelClass())->toBe(Post::class);
    expect($change->getTransition()->getOldAttributes())->toBe($oldAttributes);
});


test('it can record model soft deleted', function () {
    $post = $this->createAPost(SoftDeletedPostWithEloquentMemory::class);
    $oldAttributes = [$post->getDeletedAtColumn() => $post->getAttribute($post->getDeletedAtColumn())];
    $post->delete();
    $attributes = [$post->getDeletedAtColumn() => $post->getAttributeValue($post->getDeletedAtColumn())->format('Y-m-d\TH:i:s.u\Z')];
    /** @var Timeline $timeline */
    $this->timeline = app(TransitionRepository::class)->find(['batch' => $this->batchId]);

    $change = $this->timeline->current();
    expect($change->getTransition()->getType())->toBe('model-soft-deleted');
    expect($change->getTransition()->getModelClass())->toBe(SoftDeletedPostWithEloquentMemory::class);
    expect($change->getTransition()->getModelKey())->toBe($post->id);
    expect($change->getTransition()->getOldAttributes())->toBe($oldAttributes);
    expect($change->getTransition()->getAttributes())->toBe($attributes);
    $this->timeline->next();
    $change = $this->timeline->current();
    expect($change->getTransition()->getType())->toBe('model-created');
});

test('it can record model restored', function () {
    $post = $this->createAPost(SoftDeletedPostWithEloquentMemory::class);
    $post->delete();
    $oldAttributes = [$post->getDeletedAtColumn() => $post->getAttributeValue($post->getDeletedAtColumn())->format('Y-m-d H:i:s')];
    $post->restore();
    $attributes = [$post->getDeletedAtColumn() => $post->getAttribute($post->getDeletedAtColumn())];

    /** @var Timeline $timeline */
    $this->timeline = app(TransitionRepository::class)->find(['batch' => $this->batchId]);

    $change = $this->timeline->current();
    expect($change->getTransition()->getType())->toBe('model-restored');
    expect($change->getTransition()->getModelClass())->toBe(SoftDeletedPostWithEloquentMemory::class);
    expect($change->getTransition()->getModelKey())->toBe($post->id);
    expect($change->getTransition()->getOldAttributes())->toBe($oldAttributes);
    expect($change->getTransition()->getAttributes())->toBe($attributes);
    $this->timeline->next();
    $change = $this->timeline->current();
    expect($change->getTransition()->getType())->toBe('model-soft-deleted');
    $this->timeline->next();
    $change = $this->timeline->current();
    expect($change->getTransition()->getType())->toBe('model-created');
});
