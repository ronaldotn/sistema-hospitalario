<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Consent>
 */
class ConsentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $validFrom = $this->faker->dateTimeBetween('-2 years', '-1 month');
        $validUntil = $this->faker->dateTimeBetween('now', '+2 years');
        
        return [
            'patient_id' => $this->faker->numberBetween(1, 50),
            'granted_to' => $this->faker->numberBetween(1, 10),
            'scope' => $this->faker->randomElement(['completo', 'parcial']),
            'valid_from' => $validFrom->format('Y-m-d'),
            'valid_until' => $validUntil->format('Y-m-d'),
            'revoked' => $this->faker->boolean(10), // 10% revocados
            'created_at' => $validFrom,
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'revoked' => false,
            'valid_until' => $this->faker->dateTimeBetween('+1 month', '+2 years')->format('Y-m-d'),
        ]);
    }
}
