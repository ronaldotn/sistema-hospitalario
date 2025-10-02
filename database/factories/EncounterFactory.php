<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Encounter>
 */
class EncounterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = ['consulta', 'emergencia', 'hospitalización'];
        $statuses = ['in-progress', 'finished', 'cancelled'];
        
        $reasons = [
            'Dolor abdominal', 'Fiebre y malestar general', 'Control de rutina',
            'Dolor de cabeza intenso', 'Tos y dificultad respiratoria',
            'Dolor de pecho', 'Lesión traumática', 'Control prenatal',
            'Dolor de garganta', 'Mareos y náuseas', 'Examen preventivo',
            'Seguimiento post-operatorio', 'Dolor articular', 'Erupción cutánea'
        ];
        
        return [
            'patient_id' => $this->faker->numberBetween(1, 50),
            'practitioner_id' => $this->faker->numberBetween(1, 20),
            'organization_id' => $this->faker->numberBetween(1, 10),
            'encounter_date' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'encounter_type' => $this->faker->randomElement($types),
            'reason' => $this->faker->randomElement($reasons),
            'status' => $this->faker->randomElement($statuses),
        ];
    }

    public function consultation(): static
    {
        return $this->state(fn (array $attributes) => [
            'encounter_type' => 'consulta',
            'status' => 'finished',
        ]);
    }

    public function emergency(): static
    {
        return $this->state(fn (array $attributes) => [
            'encounter_type' => 'emergencia',
            'status' => 'in-progress',
        ]);
    }
}
