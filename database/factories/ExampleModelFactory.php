<?php

namespace Debuqer\EloquentMemory\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;


class ExampleModelFactory extends Factory
{
    protected $model = \Debuqer\EloquentMemory\Tests\Example\ExampleModel::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'meta' => [
                'name' => $this->faker->name,
                'city' => $this->faker->city,
                'country' => $this->faker->country,
            ],
        ];
    }
}

