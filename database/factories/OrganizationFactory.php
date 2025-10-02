<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Organization>
 */
class OrganizationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement(['hospital', 'laboratorio', 'farmacia']);
        
        return [
            'name' => $this->generateOrganizationName($type),
            'type' => $type,
            'address' => $this->generateBolivianAddress(),
            'phone' => '2-2' . $this->faker->numerify('######'),
            'email' => $this->faker->unique()->companyEmail(),
            'created_at' => now(),
        ];
    }

        private function generateOrganizationName(string $type): string
    {
        $prefixes = [
            'hospital' => ['Hospital', 'Clínica', 'Centro Médico', 'Instituto'],
            'laboratorio' => ['Laboratorio', 'Lab', 'Laboratorios'],
            'farmacia' => ['Farmacia', 'Botica']
        ];
        
        $names = [
            'San Juan', 'La Paz', 'Boliviano', 'Santa Cruz', 'del Sur',
            'Central', 'San José', 'Miraflores', 'Los Andes', 'Universitario'
        ];
        
        return $this->faker->randomElement($prefixes[$type]) . ' ' . 
               $this->faker->randomElement($names);
    }

    private function generateBolivianAddress(): string
    {
        $zones = ['Sopocachi', 'Calacoto', 'San Miguel', 'Centro', 'Villa Fátima'];
        $streets = ['Av. 6 de Agosto', 'Av. Arce', 'Calle Comercio', 'Av. Saavedra'];
        
        return $this->faker->randomElement($zones) . ', ' . 
               $this->faker->randomElement($streets) . ' #' . 
               $this->faker->numberBetween(100, 5000);
    }
}
