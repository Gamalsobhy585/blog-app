<?php

namespace Database\Factories;

use App\Models\Author;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AuthorFactory extends Factory
{
    protected $model = Author::class;

    public function definition(): array
    {
        $name = $this->faker->name();

        return [
            'uuid' => (string) Str::uuid(),

            'name' => $name,

            'bio' => $this->faker->optional()->paragraph(),

            // Production-like slug
            'slug' => Str::slug($name) . '-' . Str::random(6),

            'nationality' => $this->faker->optional()->country(),

            // Seeder Requirements
            'is_approved' => 0,
            'approval_status' => 2, // pending

            'created_by' => 2, // librarian user id
        ];
    }
}