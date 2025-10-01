<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Observation>
 */
class ObservationFactory extends Factory
{
    private array $observations = [
        ['code' => '8480-6', 'type' => 'blood_pressure_systolic', 'unit' => 'mmHg'],
        ['code' => '8462-4', 'type' => 'blood_pressure_diastolic', 'unit' => 'mmHg'],
        ['code' => '8867-4', 'type' => 'heart_rate', 'unit' => 'bpm'],
        ['code' => '8310-5', 'type' => 'temperature', 'unit' => '°C'],
        ['code' => '29463-7', 'type' => 'weight', 'unit' => 'kg'],
        ['code' => '8302-2', 'type' => 'height', 'unit' => 'cm'],
        ['code' => '2093-3', 'type' => 'cholesterol', 'unit' => 'mg/dL'],
        ['code' => '2345-7', 'type' => 'glucose', 'unit' => 'mg/dL'],
        ['code' => '718-7', 'type' => 'hemoglobin', 'unit' => 'g/dL'],
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $observation = $this->faker->randomElement($this->observations);
        
        return [
            'encounter_id' => $this->faker->numberBetween(1, 100),
            'patient_id' => $this->faker->numberBetween(1, 50),
            'code' => $observation['code'],
            'value' => $this->generateValueForType($observation['type']),
            'unit' => $observation['unit'],
            'observed_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
        ];
    }

    private function generateValueForType(string $type): string
    {
        return match($type) {
            'blood_pressure_systolic' => (string) $this->faker->numberBetween(90, 180),
            'blood_pressure_diastolic' => (string) $this->faker->numberBetween(60, 110),
            'heart_rate' => (string) $this->faker->numberBetween(60, 120),
            'temperature' => number_format($this->faker->randomFloat(1, 36.0, 39.5), 1),
            'weight' => number_format($this->faker->randomFloat(1, 45, 120), 1),
            'height' => (string) $this->faker->numberBetween(150, 190),
            'cholesterol' => (string) $this->faker->numberBetween(150, 280),
            'glucose' => (string) $this->faker->numberBetween(70, 200),
            'hemoglobin' => number_format($this->faker->randomFloat(1, 11.0, 17.0), 1),
            default => (string) $this->faker->numberBetween(1, 100),
        };
    }
}
