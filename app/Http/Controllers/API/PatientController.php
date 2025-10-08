<?php

namespace App\Http\Controllers\API;

use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\API\BaseController;

class PatientController extends BaseController
{
    /**
     * Listar todos los pacientes
     */
 public function index(Request $request): JsonResponse
    {
        // ======================================
        // 🔹 0️⃣ Valores por defecto y seguridad
        // ======================================
        $count      = (int) ($request->_count ?? 10);            // Registros por página
        $firstName  = trim($request->input('first_name', ''));   // Filtro nombre
        $lastName   = trim($request->input('last_name', ''));    // Filtro apellido
        $active     = $request->has('active') ? $request->boolean('active') : null; // Filtro estado

        // ======================================
        // 🔹 1️⃣ Construir query base
        // ======================================
        $query = Patient::query();

        // ======================================
        // 🔹 2️⃣ Aplicar filtros dinámicos
        // ======================================
        if (!empty($firstName)) {
            $query->where('first_name', 'like', "%{$firstName}%");
        }

        if (!empty($lastName)) {
            $query->where('last_name', 'like', "%{$lastName}%");
        }

        if (!is_null($active)) {
            $query->where('active', $active);
        }

        // 🔮 Futuro: specialty, organization_id, rangos de fechas, etc.
        // if ($specialty = $request->input('specialty')) {
        //     $query->where('specialty', $specialty);
        // }

        // ======================================
        // 🔹 3️⃣ Ordenamiento jerárquico
        // ======================================
        // Primero por fecha de creación DESC, luego por nombre ASC como desempate
        $query->orderBy('created_at', 'desc')
            ->orderBy('first_name', 'asc');

        // ======================================
        // 🔹 4️⃣ Paginación automática
        // ======================================
        // paginate() maneja automáticamente offset, total, páginas
        $practitioners = $query->paginate($count);

        // ======================================
        // 🔹 5️⃣ Devolver respuesta JSON
        // ======================================
        return $this->sendResponse($practitioners, 'Lista de profesionales filtrada y ordenada');
    }

    /**
     * Crear un nuevo paciente
     */
    public function store(Request $request)
    {
        // Validación mínima de campos esenciales
        $validated = $request->validate([
            'identifier' => 'required|unique:patients,identifier',
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other,unknown',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string',
        ]);

        // Crear paciente en la base de datos
        $patient = Patient::create($validated);

        // Respuesta JSON
        return response()->json([
            'message' => 'Paciente creado exitosamente',
            'data' => $patient
        ], 201);
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
}
