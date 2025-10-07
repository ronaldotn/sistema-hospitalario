<?php

namespace App\Http\Controllers\API;

use App\Models\Patient; // Modelo de pacientes
use Illuminate\Http\Request; // Maneja las solicitudes HTTP
use App\Http\Controllers\API\BaseController as BaseController;

class PatientController extends BaseController
{
    /**
     * Listar todos los pacientes
     */
    public function index()
    {
        // Trae todos los pacientes de la base de datos
        $patients = Patient::all();

        // Devuelve JSON con mensaje y datos
        return response()->json([
            'message' => 'Listado de pacientes',
            'data' => $patients
        ], 200);
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
