<?php

namespace App\Http\Controllers\API;

use App\Models\Patient;
use App\Models\PatientHistory;
use App\Models\AuditEvents;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class PatientController extends BaseController
{
    /**
     * 🟢 Listar pacientes
     * - Búsqueda por nombre o documento
     * - Paginación (_count, _offset)
     * - Edad calculada en el modelo
     */
    public function index(Request $request): JsonResponse
    {
        $result = Patient::searchWithAge($request->only(
            'identifier', 'name', '_count', '_offset'
        ));

        return $this->sendResponse(
            $result,
            'Lista de pacientes con edad calculada',
            200
        );
    }

    /**
     * 🟢 Crear un nuevo paciente
     * - Valida nombre/alias, fecha/edad, documento
     * - Genera identificador FHIR
     * - Registra auditoría
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'nombre'              => 'required_without:alias|string|max:255|nullable',
                'apellidos'           => 'required_without:alias|string|max:255|nullable',
                'alias'               => 'nullable|string|max:255',
                'documento_identidad' => 'nullable|string|max:255|unique:patients,documento_identidad',
                'fecha_nacimiento'    => 'nullable|date',
                'edad_estimado'       => 'nullable|integer|min:0',
                'sexo'                => 'nullable|string|in:masculino,femenino,otro',
                'direccion'           => 'nullable|string|max:255',
                'contacto'            => 'nullable|string|max:255',
                'correo'              => 'nullable|email|max:255|unique:patients,correo',
            ]);

            if (empty($validatedData['nombre']) && empty($validatedData['alias'])) {
                return $this->sendError('Debe ingresar nombre/apellido o alias', [], 422);
            }
            if (empty($validatedData['fecha_nacimiento']) && empty($validatedData['edad_estimado'])) {
                return $this->sendError('Debe ingresar fecha de nacimiento o edad estimada', [], 422);
            }
            if (empty($validatedData['documento_identidad'])) {
                return $this->sendError('Debe ingresar al menos un documento o identificador', [], 422);
            }

            DB::beginTransaction();

            $fhirIdentifier = [
                'system' => env('FHIR_SYSTEM_URL', 'http://localhost/fhir/Patient'),
                'value'  => Str::uuid()->toString(),
                'type'   => [
                    'coding' => [[
                        'system'  => 'http://terminology.hl7.org/CodeSystem/v2-0203',
                        'code'    => 'MR',
                        'display' => 'Medical Record Number',
                    ]],
                ],
            ];

            // UUID se genera en el modelo
            $patient = Patient::create(array_merge(
                $validatedData,
                ['fhir_identifier' => $fhirIdentifier, 'version' => 1]
            ));

            AuditEvents::create([
                'usuario_id'   => Auth::id(),
                'recurso'      => 'Patient',
                'evento'       => 'create',
                'detalle'      => $patient->toArray(),
                'recurso_uuid' => $patient->uuid,
            ]);

            DB::commit();
            return $this->sendResponse($patient, 'Paciente registrado exitosamente', 201);

        } catch (ValidationException $e) {
            return $this->sendError('Error de validación', $e->errors(), 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Error al crear paciente', [$e->getMessage()], 500);
        }
    }

    /**
     * 🟢 Ver un paciente específico
     */
    public function show(string $uuid): JsonResponse
    {
        $patient = Patient::where('uuid', $uuid)->first();

        if (!$patient) {
            return $this->sendError('Paciente no encontrado', [], 404);
        }

        // Añadimos edad calculada al vuelo
        $patient->edad = $patient->edad;

        return $this->sendResponse($patient, 'Paciente encontrado');
    }

    /**
     * 🟠 Actualizar datos de un paciente
     * - Guarda historial en PatientHistory
     * - Aumenta versión
     * - Registra auditoría
     */
    public function update(Request $request, string $uuid): JsonResponse
    {
        $patient = Patient::where('uuid', $uuid)->first();
        if (!$patient) {
            return $this->sendError('Paciente no encontrado', [], 404);
        }

        try {
            $validatedData = $request->validate([
                'nombre'              => 'nullable|string|max:255',
                'apellidos'           => 'nullable|string|max:255',
                'alias'               => 'nullable|string|max:255',
                'documento_identidad' => 'nullable|string|max:255|unique:patients,documento_identidad,' . $patient->id,
                'fecha_nacimiento'    => 'nullable|date',
                'edad_estimado'       => 'nullable|integer|min:0',
                'sexo'                => 'nullable|string|in:masculino,femenino,otro',
                'direccion'           => 'nullable|string|max:255',
                'contacto'            => 'nullable|string|max:255',
                'correo'              => 'nullable|email|max:255|unique:patients,correo,' . $patient->id,
            ]);

            DB::beginTransaction();

            // Guardamos versión anterior
            PatientHistory::create([
                'patient_id' => $patient->id,
                'data'       => $patient->toArray(),
                'version'    => $patient->version,
            ]);

            $patient->update(array_merge(
                $validatedData,
                ['version' => $patient->version + 1]
            ));

            AuditEvents::create([
                'usuario_id'   => Auth::id(),
                'recurso'      => 'Patient',
                'evento'       => 'update',
                'detalle'      => $patient->toArray(),
                'recurso_uuid' => $patient->uuid,
            ]);

            DB::commit();
            return $this->sendResponse($patient, 'Paciente actualizado exitosamente');

        } catch (ValidationException $e) {
            return $this->sendError('Error de validación', $e->errors(), 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Error al actualizar paciente', [$e->getMessage()], 500);
        }
    }

    /**
     * 🔴 Eliminación lógica de paciente (soft delete)
     * - Conserva historial y registra auditoría
     */
    public function destroy(string $uuid): JsonResponse
    {
        $patient = Patient::where('uuid', $uuid)->first();
        if (!$patient) {
            return $this->sendError('Paciente no encontrado', [], 404);
        }

        DB::beginTransaction();
        try {
            $patient->delete(); // Soft delete: marca deleted_at

            AuditEvents::create([
                'usuario_id'   => Auth::id(),
                'recurso'      => 'Patient',
                'evento'       => 'delete',
                'detalle'      => $patient->toArray(),
                'recurso_uuid' => $patient->uuid,
            ]);

            DB::commit();
            return $this->sendResponse(null, 'Paciente eliminado (borrado lógico)');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Error al eliminar paciente', [$e->getMessage()], 500);
        }
    }
}
