<?php

namespace App\Http\Controllers\API;

use App\Models\Condition;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\API\BaseController as BaseController;

class ConditionController extends BaseController
{
    /**
     * ======================================
     * 🔹 INDEX – Listar condiciones
     * ======================================
     * Permite filtrar por paciente o encounter, paginar y ordenar.
     * Query params disponibles:
     * - patient_id
     * - encounter_id
     * - _count (default 10)
     * - _page (manejado por paginate)
     */
    public function index(Request $request): JsonResponse
    {
        $count = (int) ($request->_count ?? 10);
        $patientId = $request->input('patient_id');
        $encounterId = $request->input('encounter_id');

        $query = Condition::query();

        if ($patientId) {
            $query->where('patient_id', $patientId);
        }

        if ($encounterId) {
            $query->where('encounter_id', $encounterId);
        }

        $query->orderBy('recorded_date', 'desc')
              ->orderBy('id', 'desc');

        $conditions = $query->paginate($count);

        return $this->sendResponse($conditions, 'Lista de condiciones obtenida correctamente');
    }

    /**
     * ======================================
     * 🔹 STORE – Crear condición
     * ======================================
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'patient_id'   => 'required|exists:patients,id',
            'encounter_id' => 'nullable|exists:encounters,id',
            'code'         => 'nullable|string|max:50',
            'description'  => 'nullable|string',
            'recorded_date'=> 'nullable|date',
        ]);

        $condition = Condition::create($validated);

        return $this->sendResponse($condition, 'Condición creada correctamente');
    }

    /**
     * ======================================
     * 🔹 SHOW – Mostrar condición específica
     * ======================================
     */
 public function show(Condition $condition): JsonResponse
{
    $condition->load('patient', 'encounter'); // Evita N+1
    return $this->sendResponse($condition, 'Condición obtenida correctamente');
}


    /**
     * ======================================
     * 🔹 UPDATE – Actualizar condición
     * ======================================
     */
    public function update(Request $request, Condition $condition): JsonResponse
    {
        $validated = $request->validate([
            'patient_id'   => 'nullable|exists:patients,id',
            'encounter_id' => 'nullable|exists:encounters,id',
            'code'         => 'nullable|string|max:50',
            'description'  => 'nullable|string',
            'recorded_date'=> 'nullable|date',
        ]);

        $condition->update($validated);

        return $this->sendResponse($condition, 'Condición actualizada correctamente');
    }

    /**
     * ======================================
     * 🔹 DESTROY – Eliminar condición
     * ======================================
     */
    public function destroy(Condition $condition): JsonResponse
    {
        $condition->delete();

        return $this->sendResponse([], 'Condición eliminada correctamente');
    }
}
