<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Practitioner>
 */
class PractitionerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $specialties = [
            'Cardiología', 'Pediatría', 'Ginecología', 'Medicina General',
            'Traumatología', 'Dermatología', 'Oftalmología', 'Neurología',
            'Gastroenterología', 'Endocrinología', 'Psiquiatría', 'Urología',
            'Laboratorio Clínico', 'Radiología', 'Anestesiología'
        ];

        $gender = $this->faker->randomElement(['male', 'female']);
        $prefix = $gender === 'male' ? 'Dr.' : 'Dra.';
        
        return [
            'identifier' => 'MED-' . $this->faker->unique()->numerify('#####'),
            'first_name' => $prefix . ' ' . $this->faker->firstName($gender),
            'last_name' => $this->faker->lastName() . ' ' . $this->faker->lastName(),
            'specialty' => $this->faker->randomElement($specialties),
            'phone' => '7' . $this->faker->numerify('#######'),
            'email' => $this->faker->unique()->safeEmail(),
            'organization_id' => $this->faker->numberBetween(1, 10),
            'active' => $this->faker->boolean(95), // 95% activos
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
