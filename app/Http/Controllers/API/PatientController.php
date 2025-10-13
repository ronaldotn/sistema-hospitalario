<?php

namespace App\Http\Controllers\API;


use App\Models\Patient;
use App\Models\AuditEvents;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\API\BaseController;

class PatientController extends BaseController
{
    /**
     * Listar todos los pacientes
     */
    public function index(Request $request): JsonResponse
    {
        // ===========================
        // 🧭 1️⃣ Parámetros de entrada
        // ===========================
        $firstName   = trim($request->input('first_name', ''));  // Nombre
        $lastName    = trim($request->input('last_name', ''));   // Apellido
        $identifier  = trim($request->input('identifier', ''));  // Documento
        $birthdate   = trim($request->input('birthdate', ''));   // Fecha de nacimiento
        $phone       = trim($request->input('phone', ''));       // Teléfono
        $address     = trim($request->input('address', ''));     // Dirección
        $count      = (int) ($request->_count ?? 10);            // Registros por página

        // ======================================
        // 🔹 1️⃣ Construir query base
        // ======================================
        $query = Patient::query();

        // ===========================
        // 🧮 2️⃣ Construcción dinámica de la consulta
        // ===========================
        $query = Patient::query();

        // Filtrar por nombre (LIKE para coincidencias parciales)
        if (!empty($firstName)) {
            $query->where('first_name', 'LIKE', "%{$firstName}%");
        }

        // Filtrar por apellido
        if (!empty($lastName)) {
            $query->where('last_name', 'LIKE', "%{$lastName}%");
        }

        // Filtrar por documento exacto
        if (!empty($identifier)) {
            $query->where('identifier', $identifier);
        }

        // Filtrar por fecha de nacimiento exacta
        if (!empty($birthdate)) {
            $query->whereDate('date_of_birth', $birthdate);
        }

        // Filtrar por teléfono
        if (!empty($phone)) {
            $query->where('phone', 'LIKE', "%{$phone}%");
        }

        // Filtrar por dirección (parcial)
        if (!empty($address)) {
            $query->where('address', 'LIKE', "%{$address}%");
        }

        // Filtros faceted
        if ($request->filled('is_active')) {
            $query->whereNotNull('email'); // ejemplo
        }
        if ($ageRange = $request->input('ageRange')) {
            [$min, $max] = explode('-', $ageRange);
            $query->whereBetween('date_of_birth', [now()->subYears($max), now()->subYears($min)]);
        }

        // Ordenamiento
        $query->orderBy('created_at', 'desc');


        // ======================================
        // 🔹 4️⃣ Paginación automática
        // ======================================
        // paginate() maneja automáticamente offset, total, páginas
        $patients = $query->paginate($count);

        // ======================================
        // 🔹 5️⃣ Devolver respuesta JSON
        // ======================================
        return $this->sendResponse($patients, 'Lista de pacientes filtrada y ordenada');
    }


    /**
     * 🔹 Registrar nuevo paciente (FHIR Compatible)
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'identifier'     => ['required', 'regex:/^[0-9]{4,10}-[A-Z]{1,3}$/', 'unique:patients,identifier'],
            'first_name'     => 'required|string|max:100',
            'last_name'      => 'required|string|max:100',
            'date_of_birth'  => 'required|date|before:today',
            'gender'         => 'required|in:male,female,other,unknown',
            'phone'          => 'nullable|regex:/^[0-9]{7,15}$/',
            'email'          => 'nullable|email|max:100',
            'address'        => 'nullable|string|max:255',
        ]);

        // 🔍 Verificación adicional de duplicados
        $duplicate = Patient::where('first_name', $validated['first_name'])
            ->where('last_name', $validated['last_name'])
            ->where('date_of_birth', $validated['date_of_birth'])
            ->first();

        if ($duplicate) {
            return $this->sendError(
                'Ya existe un paciente con el mismo nombre y fecha de nacimiento.',
                409,
                ['existing' => $duplicate]
            );
        }

        // Crear paciente
        $patient = Patient::create($validated);

        // 🕵️‍♂️ Auditoría (seguridad y trazabilidad)
        AuditEvents::create([
            'user_id'   => Auth::id(),
            'action'    => 'create',
            'resource'  => 'Patient/' . $patient->id,
            'timestamp' => now(),
            'details'   => [
                'created_by' => Auth::user()->name ?? 'System',
                'timestamp'  => now()->toIso8601String(), // ISO8601
                // 'ip'         => $request->ip(),
                // 'user_agent' => $request->header('User-Agent'),
            ],
        ]);

        return $this->sendResponse($patient, 'Paciente creado exitosamente.', 201);
    }

    /**
     * Mostrar un paciente específico
     */
    public function show($id)
    {
        $patient = Patient::find($id);

        if (!$patient) {
            return response()->json([
                'message' => 'Paciente no encontrado'
            ], 404);
        }

        return response()->json([
            'message' => 'Paciente encontrado',
            'data' => $patient
        ], 200);
    }

    /**
     * Actualizar un paciente existente
     */
    public function update(Request $request, $id)
    {
        // Buscar paciente por id
        $patient = Patient::find($id);

        if (!$patient) {
            return response()->json([
                'message' => 'Paciente no encontrado'
            ], 404);
        }

        // Validación de campos que pueden actualizarse
        $validated = $request->validate([
            'identifier' => 'sometimes|required|unique:patients,identifier,' . $id,
            'first_name' => 'sometimes|required|string|max:100',
            'last_name'  => 'sometimes|required|string|max:100',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other,unknown',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string',
        ]);

        // Actualizar paciente
        $patient->update($validated);

        // Respuesta JSON
        return response()->json([
            'message' => 'Paciente actualizado exitosamente',
            'data' => $patient
        ], 200);
    }

    /**
     * Eliminar un paciente
     */
    public function destroy($id)
    {
        // Buscar paciente por id
        $patient = Patient::find($id);

        if (!$patient) {
            return response()->json([
                'message' => 'Paciente no encontrado'
            ], 404);
        }

        // Eliminar paciente
        $patient->delete();

        // Respuesta JSON
        return response()->json([
            'message' => 'Paciente eliminado exitosamente'
        ], 200);
    }
    /**
 * 🔹 Obtener métricas generales de pacientes
 */
public function metrics(): JsonResponse
{
    // 🔹 Total de pacientes
    $totalPatients = Patient::count();

    // 🔹 Pacientes que tienen al menos un encounter (consultas/hospitalizaciones)
    $patientsWithEncounters = Patient::has('encounters')->count();

    // 🔹 Pacientes que tienen alguna condición registrada
    $patientsWithConditions = Patient::has('conditions')->count();

    // 🔹 Pacientes con observaciones (ej. signos vitales o laboratorios)
    $patientsWithObservations = Patient::has('observations')->count();

    return $this->sendResponse([
        'totalPatients'            => $totalPatients,
        'patientsWithEncounters'   => $patientsWithEncounters,
        'patientsWithConditions'   => $patientsWithConditions,
        'patientsWithObservations' => $patientsWithObservations,
    ], 'Métricas de pacientes obtenidas');
}

}
