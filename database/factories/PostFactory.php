<?php

namespace Debuqer\EloquentMemory\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;


class PostFactory extends Factory
{
    protected $model = \Debuqer\EloquentMemory\Tests\Example\Post::class;

    public function definition()
    {
        return [
            'title' => $this->faker->name(),
            'owner_id' => $this->faker->numberBetween(1, 10),
            'content' => $this->faker->realText,
            'meta' => [
                'name' => $this->faker->name,
                'city' => $this->faker->city,
                'country' => $this->faker->country,
            ],
        ];
    }
}

