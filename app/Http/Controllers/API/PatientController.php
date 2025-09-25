<?php

namespace App\Http\Controllers\API;

use App\Models\Patient;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;

class PatientController extends BaseController
{
    /**
     * Listado de pacientes con edad calculada
     */
    public function index(): JsonResponse
    {
        $patients = Patient::allWithAge(); // llamamos al método del modelo
        return $this->sendResponse($patients, 'Lista de pacientes con edad', 200);
    }

    /**
     * Crear un nuevo paciente
     */
    public function store(Request $request): JsonResponse
    {
        // 🔒 Paso 0: Validación de autorización (scope patient.write)
        // $this->authorize('patient.write'); // Si usas Laravel Passport/Sanctum

        // Paso 1: Validar los datos de entrada
        try {
            $validatedData = $request->validate([
                'nombre' => 'required_without:alias|string|max:255|nullable',
                'apellidos' => 'required_without:alias|string|max:255|nullable',
                'alias' => 'nullable|string|max:255',
                'documento_identidad' => 'nullable|string|max:255|unique:patients,documento_identidad',
                'fecha_nacimiento' => 'nullable|date',
                'edad_estimado' => 'nullable|integer|min:0',
                'sexo' => 'nullable|string|in:masculino,femenino,otro',
                'direccion' => 'nullable|string|max:255',
                'contacto' => 'nullable|string|max:255',
                'correo' => 'nullable|email|max:255|unique:patients,correo',
            ]);


            // Validación condicional: nombre/apellido o alias
            if (empty($validatedData['nombre']) && empty($validatedData['alias'])) {
                return $this->sendError('Debe ingresar nombre/apellido o alias', [], 422);
            }

            // Validación condicional: fecha nacimiento o edad estimada
            if (empty($validatedData['fecha_nacimiento']) && empty($validatedData['edad_estimado'])) {
                return $this->sendError('Debe ingresar fecha de nacimiento o edad estimada', [], 422);
            }

            // Al menos un identificador o documento
            if (empty($validatedData['documento_identidad'])) {
                return $this->sendError('Debe ingresar al menos un documento o identificador', [], 422);
            }
        } catch (ValidationException $e) {
            return $this->sendError('Error de validación', $e->errors(), 422);
        }

        // Paso 2: Validación rápida de duplicados
        $existingPatient = Patient::where('documento_identidad', $validatedData['documento_identidad'])
            ->orWhere(function ($query) use ($validatedData) {
                $query->where('nombre', $validatedData['nombre'] ?? '')
                    ->where('apellidos', $validatedData['apellidos'] ?? '')
                    ->where('fecha_nacimiento', $validatedData['fecha_nacimiento'] ?? null);
            })->first();

        if ($existingPatient) {
            return $this->sendError(
                'El paciente ya existe en el sistema.',
                ['uuid' => $existingPatient->uuid],
                409
            );
        }

        // Paso 3: Crear identificador FHIR usando .env
        $fhirIdentifier = [
            'system' => env('FHIR_SYSTEM_URL', 'http://localhost/fhir/Patient'),
            'value' => Str::uuid()->toString(),
            'type' => [
                'coding' => [
                    [
                        'system' => 'http://terminology.hl7.org/CodeSystem/v2-0203',
                        'code' => 'MR',
                        'display' => 'Medical Record Number'
                    ]
                ]
            ]
        ];

        // Paso 4: Persistir en la base de datos
        $patient = Patient::create(array_merge($validatedData, [
            'uuid' => Str::uuid()->toString(),
            'fhir_identifier' => $fhirIdentifier
        ]));

        // Paso 5: Auditoría mínima (sin PII)
        // AuditEvent::create([
        //     'user_id' => auth()->id(),
        //     'action' => 'create_patient',
        //     'ip' => $request->ip(),
        //     'payload' => json_encode(['uuid' => $patient->uuid]),
        // ]);

        // Paso 6: Respuesta exitosa
        return $this->sendResponse($patient, 'Paciente registrado exitosamente', 201);
    }

    /**
     * Mostrar paciente por UUID
     */
    public function show(string $uuid): JsonResponse
    {
        $patient = Patient::where('uuid', $uuid)->first();

        if (!$patient) {
            return $this->sendError('Paciente no encontrado', [], 404);
        }

        return $this->sendResponse($patient, 'Paciente encontrado', 200);
    }

    /**
     * Actualizar paciente (por implementar)
     */
    public function update(Request $request, Patient $patient): JsonResponse
    {
        // Aquí se implementaría update con validaciones similares
        return $this->sendError('Funcionalidad aún no implementada', [], 501);
    }

    /**
     * Eliminar paciente por UUID
     */
    public function destroy(string $uuid): JsonResponse
    {
        $patient = Patient::where('uuid', $uuid)->first();

        if (!$patient) {
            return $this->sendError('Paciente no encontrado', [], 404);
        }

        try {
            $patient->delete();
            return $this->sendResponse([], 'Paciente eliminado exitosamente', 200);
        } catch (\Exception $e) {
            return $this->sendError('Error eliminando paciente', [$e->getMessage()], 500);
        }
    }
}
