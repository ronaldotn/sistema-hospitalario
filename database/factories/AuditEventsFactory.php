<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AuditEvents>
 */
class AuditEventsFactory extends Factory
{
    private array $actions = [
        ['action' => 'LOGIN', 'resources' => ['system']],
        ['action' => 'LOGOUT', 'resources' => ['system']],
        ['action' => 'VIEW', 'resources' => ['patients', 'encounters', 'reports']],
        ['action' => 'CREATE', 'resources' => ['patients', 'encounters', 'observations']],
        ['action' => 'UPDATE', 'resources' => ['patients', 'encounters', 'conditions']],
        ['action' => 'DELETE', 'resources' => ['observations', 'reports']],
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $actionData = $this->faker->randomElement($this->actions);
        $resource = $this->faker->randomElement($actionData['resources']);
        $resourceId = $resource !== 'system' ? '/' . $this->faker->numberBetween(1, 100) : '';
        
        return [
            'user_id' => $this->faker->numberBetween(1, 20),
            'action' => $actionData['action'],
            'resource' => $resource . $resourceId,
            'timestamp' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'details' => json_encode($this->generateDetails($actionData['action'])),
        ];
    }

    private function generateDetails(string $action): array
    {
        return match($action) {
            'LOGIN', 'LOGOUT' => [
                'ip' => $this->faker->ipv4(),
                'user_agent' => $this->faker->userAgent(),
            ],
            'VIEW' => [
                'access_reason' => $this->faker->randomElement([
                    'Consulta médica', 'Revisión de historial', 'Seguimiento'
                ]),
            ],
            'CREATE' => [
                'created_by' => $this->faker->name(),
                'timestamp' => now()->toIso8601String(),
            ],
            'UPDATE' => [
                'changed_fields' => $this->faker->randomElements(['status', 'notes', 'date'], 2),
                'updated_by' => $this->faker->name(),
            ],
            'DELETE' => [
                'deleted_by' => $this->faker->name(),
                'reason' => 'Registro duplicado',
            ],
            default => [],
        };
    }
}
