<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DiagnosticReport>
 */
class DiagnosticReportFactory extends Factory
{
    private array $reports = [
        [
            'type' => 'Radiografía de Tórax',
            'category' => 'radiology',
            'results' => [
                'Campos pulmonares sin alteraciones visibles',
                'Silueta cardíaca dentro de límites normales',
                'Estructuras óseas sin lesiones traumáticas',
            ]
        ],
        [
            'type' => 'Electrocardiograma',
            'category' => 'cardiology',
            'results' => [
                'Ritmo sinusal regular',
                'Frecuencia cardíaca normal',
                'Sin signos de isquemia aguda',
            ]
        ],
        [
            'type' => 'Análisis de Sangre Completo',
            'category' => 'laboratory',
            'results' => [
                'Hemoglobina dentro de parámetros normales',
                'Leucocitos sin alteraciones',
                'Plaquetas en rango normal',
            ]
        ],
        [
            'type' => 'Ecografía Abdominal',
            'category' => 'ultrasound',
            'results' => [
                'Hígado de tamaño y ecogenicidad normal',
                'Vesícula biliar sin cálculos',
                'Riñones de morfología conservada',
            ]
        ],
        [
            'type' => 'Tomografía Computarizada',
            'category' => 'radiology',
            'results' => [
                'Estudio sin evidencia de lesiones ocupantes de espacio',
                'Estructuras anatómicas conservadas',
                'No se identifican hallazgos patológicos',
            ]
        ],
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $report = $this->faker->randomElement($this->reports);
        $result = $this->faker->randomElement($report['results']);
        
        return [
            'patient_id' => $this->faker->numberBetween(1, 50),
            'encounter_id' => $this->faker->numberBetween(1, 100),
            'type' => $report['type'],
            'result' => $result,
            'document' => json_encode([
                'resourceType' => 'DiagnosticReport',
                'status' => 'final',
                'category' => $report['category'],
                'conclusion' => $result,
                'issued' => $this->faker->dateTimeBetween('-6 months', 'now')->format('Y-m-d\TH:i:s\Z')
            ]),
            'created_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
        ];
    }
}
