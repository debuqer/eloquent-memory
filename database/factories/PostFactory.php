<?php

namespace Debuqer\EloquentMemory\Database\Factories;

use Debuqer\EloquentMemory\Tests\Fixtures\User;
use Illuminate\Database\Eloquent\Factories\Factory;


class PostFactory extends Factory
{
    protected $model = \Debuqer\EloquentMemory\Tests\Fixtures\Post::class;

    public function definition()
    {
        $user = self::factoryForModel(User::class)->createOne();

        return [
            'title' => $this->faker->name(),
            'owner_id' => $user->getKey(),
            'content' => $this->faker->realText,
            'meta' => [
                'name' => $this->faker->name,
                'city' => $this->faker->city,
                'country' => $this->faker->country,
            ],
        ];
    }
}

