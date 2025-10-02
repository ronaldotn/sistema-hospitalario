<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Condition>
 */
class ConditionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    private array $conditions = [
        ['code' => 'I10', 'description' => 'Hipertensión esencial (primaria)'],
        ['code' => 'E11.9', 'description' => 'Diabetes mellitus tipo 2 sin complicaciones'],
        ['code' => 'J00', 'description' => 'Rinofaringitis aguda (resfriado común)'],
        ['code' => 'J02.9', 'description' => 'Faringitis aguda, no especificada'],
        ['code' => 'K29.7', 'description' => 'Gastritis, no especificada'],
        ['code' => 'M54.5', 'description' => 'Dolor lumbar'],
        ['code' => 'R10.4', 'description' => 'Otros dolores abdominales y los no especificados'],
        ['code' => 'R51', 'description' => 'Cefalea'],
        ['code' => 'I20.0', 'description' => 'Angina inestable'],
        ['code' => 'I21.9', 'description' => 'Infarto agudo del miocardio'],
        ['code' => 'J18.9', 'description' => 'Neumonía, no especificada'],
        ['code' => 'N39.0', 'description' => 'Infección de vías urinarias'],
        ['code' => 'L30.9', 'description' => 'Dermatitis, no especificada'],
        ['code' => 'Z00.0', 'description' => 'Examen médico general'],
        ['code' => 'Z34.0', 'description' => 'Supervisión de embarazo normal'],
    ];

    public function definition(): array
    {
        $condition = $this->faker->randomElement($this->conditions);
        
        return [
            'encounter_id' => $this->faker->numberBetween(1, 100),
            'patient_id' => $this->faker->numberBetween(1, 50),
            'code' => $condition['code'],
            'description' => $condition['description'],
            'recorded_date' => $this->faker->dateTimeBetween('-6 months', 'now'),
        ];
    }
}
