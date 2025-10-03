<?php

namespace App\Http\Controllers\API;

use App\Models\Patient;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\API\BaseController as BaseController;

class PatientController extends BaseController
{
 /**
     * 🔹 GET /patients
     * Obtener lista de pacientes con filtros, ordenamiento y paginación.
     *
     * Query Params disponibles:
     * - identifier: filtra por documento de identidad (like)
     * - name: filtra por nombre o apellido (like)
     * - _count: cantidad de registros por página (default: 10)
     * - _offset: desplazamiento para paginación (default: 0)
     * - sort: columna para ordenar (default: created_at)
     * - direction: dirección 'asc' o 'desc' (default: desc)
     */
public function index(Request $request): JsonResponse
{
    // ======================================
    // 🔹 0️⃣ Inicializar variables con valores por defecto
    // ======================================
    $identifier = trim($request->input('identifier', ''));
    $name       = trim($request->input('name', ''));
    $count      = (int) ($request->_count ?? 10);
    $offset     = (int) ($request->_offset ?? 0);
    $sortColumn = $request->input('sort', 'created_at');
    $sortDirection = strtolower($request->input('direction', 'desc')) === 'asc' ? 'asc' : 'desc';

    // ======================================
    // 🔹 1️⃣ Construir query base
    // ======================================
    $query = Patient::query();

    // ======================================
    // 🔹 2️⃣ Aplicar filtros si existen
    // ======================================
    if (!empty($identifier)) {
        $query->where('identifier', 'like', "%{$identifier}%");
    }

    if (!empty($name)) {
        $query->where(function ($q) use ($name) {
            $q->where('first_name', 'like', "%{$name}%")
              ->orWhere('last_name', 'like', "%{$name}%");
        });
    }

    // 🔹 Se pueden agregar más filtros futuros aquí, siempre validando con !empty()

    // ======================================
    // 🔹 3️⃣ Aplicar ordenamiento
    // ======================================
    $query->orderBy($sortColumn, $sortDirection);

    // ======================================
    // 🔹 4️⃣ Aplicar paginación
    // ======================================
    $total = $query->count(); // Total de registros sin paginar
    $patients = $query->offset($offset)
                      ->limit($count)
                      ->get();

    // ======================================
    // 🔹 5️⃣ Preparar datos de respuesta
    // ======================================
    $data = [
        'patients' => $patients,
        'total' => $total,
        'count' => $count,
        'offset' => $offset,
        'sort' => $sortColumn,
        'direction' => $sortDirection,
    ];

    // ======================================
    // 🔹 6️⃣ Devolver respuesta usando BaseController
    // ======================================
    return $this->sendResponse($data, 'Lista de pacientes con filtros, ordenamiento y paginación');
}


    /**
     * Store a newly created resource in storage.
     */


    public function store(Request $request)
    {
        // ✅ Validar campos mínimos (puedes ajustar según tu migration)
        $validated = $request->validate([
            'identifier'    => 'required|string|max:50|unique:patients,identifier',
            'first_name'    => 'required|string|max:100',
            'last_name'     => 'required|string|max:100',
            'date_of_birth' => 'nullable|date',
            'gender'        => 'nullable|in:male,female,other,unknown',
            'phone'         => 'nullable|string|max:20',
            'email'         => 'nullable|email|max:100|unique:patients,email',
            'address'       => 'nullable|string',
        ]);

        // ✅ Crear el paciente
        $patient = Patient::create($validated);

        // ✅ Responder JSON simple
        return response()->json([
            'message' => 'Paciente creado exitosamente',
            'data'    => $patient,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $uuid)
    {

        $patient = Patient::where('uuid', $uuid)->first();
        // dd($patient);
        if (!$patient) {
            return response()->json(['message' => 'Paciente no encontrado'], 404);
        }

        return response()->json([
            'message' => 'Paciente encontrado',
            'data' => $patient
        ], 200);
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Patient $patient)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Patient $patient)
    {
        //
    }
}
