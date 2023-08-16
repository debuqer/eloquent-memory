<?php

namespace Debuqer\EloquentMemory\Tests\Fixtures\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    protected $model = \Debuqer\EloquentMemory\Tests\Fixtures\User::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name,
        ];
    }
}
