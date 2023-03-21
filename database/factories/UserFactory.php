<?php

namespace Debuqer\EloquentMemory\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;


class UserFactory extends Factory
{
    protected $model = \Debuqer\EloquentMemory\Tests\Example\User::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name,
        ];
    }
}

