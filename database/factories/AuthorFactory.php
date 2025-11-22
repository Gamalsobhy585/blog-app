<?php

namespace Database\Factories;

use App\Models\Author;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class AuthorFactory extends Factory
{
    protected $model = Author::class;

    public function definition(): array
    {
        return [
            'uuid'         => (string) Str::uuid(),
            'name'         => $this->faker->name(),
            'bio'          => $this->faker->optional()->paragraph(),
            'nationality'  => $this->faker->optional()->country(),
            'is_approved'  => $this->faker->boolean(60), 
            'created_by' => \App\Models\User::inRandomOrder()->value('id'),
            
        ];
    }
}
