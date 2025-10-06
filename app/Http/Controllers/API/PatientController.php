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
     * ======================================
     * 🔹 INDEX – Obtener lista de pacientes
     * ======================================
     * Este método entrega la lista de pacientes paginada directamente.
     * Laravel Eloquent con paginate() se encarga automáticamente de:
     * - Conteo total de registros
     * - Número de páginas
     * - Offset y página actual
     *
     * El frontend recibe únicamente la colección de pacientes lista para renderizar.
     *
     * Query Params disponibles:
     * - identifier: filtra por documento de identidad (like)
     * - name: filtra por nombre o apellido (like)
     * - _count: cantidad de registros por página (default: 10)
     * - _page: número de página (default: 1, manejado automáticamente por paginate)
     * - sort: columna para ordenar (default: created_at)
     * - direction: dirección 'asc' o 'desc' (default: desc)
     */
    public function index(Request $request): JsonResponse
    {
        // ======================================
        // 🔹 0️⃣ Valores por defecto y seguridad
        // ======================================
        $identifier    = trim($request->input('identifier', ''));
        $name          = trim($request->input('name', ''));
        $count         = (int) ($request->_count ?? 10);   // Registros por página
        $sortColumn    = $request->input('sort', 'created_at');
        $sortDirection = strtolower($request->input('direction', 'desc')) === 'asc' ? 'asc' : 'desc';

        // ======================================
        // 🔹 1️⃣ Construir query base
        // ======================================
        $query = Patient::query();

        // ======================================
        // 🔹 2️⃣ Aplicar filtros dinámicos
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

        // 🔮 Espacio futuro para filtros adicionales
        // Ej: edad, sexo, estado, doctor, rango de fechas
        // if (!empty($request->input('age'))) { ... }

        // ======================================
        // 🔹 3️⃣ Ordenamiento
        // ======================================
        $query->orderBy($sortColumn, $sortDirection);

        // ======================================
        // 🔹 4️⃣ Paginación automática
        // ======================================
        // paginate() maneja automáticamente offset, total, páginas
        $patients = $query->paginate($count);

        // ======================================
        // 🔹 5️⃣ Devolver respuesta JSON
        // ======================================
        // Solo enviamos la colección de pacientes, Eloquent incluye la info de paginación
        return $this->sendResponse($patients, 'Lista de pacientes filtrada y ordenada');
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
