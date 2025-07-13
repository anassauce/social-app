<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvitationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'sender_id' => User::factory(),
            'recipient_id' => User::factory(),
            'message' => $this->faker->optional()->sentence(),
            'status' => $this->faker->randomElement(['pending', 'accepted', 'rejected']),
            'responded_at' => $this->faker->optional()->dateTimeThisYear(),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'responded_at' => null,
        ]);
    }

    public function accepted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'accepted',
            'responded_at' => $this->faker->dateTimeThisYear(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
            'responded_at' => $this->faker->dateTimeThisYear(),
        ]);
    }
}