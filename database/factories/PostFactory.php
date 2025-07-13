<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => $this->faker->sentence(6),
            'content' => $this->faker->paragraphs(3, true),
            'is_ai_generated' => $this->faker->boolean(20),
            'ai_prompt' => $this->faker->optional()->sentence(),
            'status' => $this->faker->randomElement(['draft', 'published']),
            'visibility' => $this->faker->randomElement(['public', 'connections', 'private']),
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'published',
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }

    public function public(): static
    {
        return $this->state(fn (array $attributes) => [
            'visibility' => 'public',
        ]);
    }

    public function private(): static
    {
        return $this->state(fn (array $attributes) => [
            'visibility' => 'private',
        ]);
    }

    public function aiGenerated(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_ai_generated' => true,
            'ai_prompt' => $this->faker->sentence(),
        ]);
    }
}