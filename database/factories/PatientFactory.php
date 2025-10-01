<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Patient>
 */
class PatientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'identifier' => $this->faker->unique()->numerify('#######') . '-LP',
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName() . ' ' . $this->faker->lastName(),
            'date_of_birth' => $this->faker->dateTimeBetween('-80 years', '-18 years')->format('Y-m-d'),
            'gender' => $this->faker->randomElement(['male', 'female']),
            'phone' => '7' . $this->faker->numerify('#######'),
            'email' => $this->faker->unique()->safeEmail(),
            'address' => $this->generateBolivianAddress(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    private function generateBolivianAddress(): string
    {
        $zones = [
            'Sopocachi', 'Calacoto', 'San Miguel', 'Miraflores', 
            'Villa Fátima', 'Alto San Pedro', 'Achumani', 'Obrajes',
            'Centro', 'San Jorge', 'Irpavi', 'Cota Cota'
        ];
        
        $streets = [
            'Av. 6 de Agosto', 'Calle Comercio', 'Av. Arce', 
            'Calle Ecuador', 'Av. Saavedra', 'Calle México',
            'Av. Costanera', 'Calle 21', 'Av. Busch'
        ];
        
        return $this->faker->randomElement($zones) . ', ' . 
               $this->faker->randomElement($streets) . ' #' . 
               $this->faker->numberBetween(100, 9999);
    }
}
