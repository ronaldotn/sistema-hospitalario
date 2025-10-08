<?php

namespace App\Http\Controllers\API;

use App\Models\Encounter;
use App\Models\Patient;
use App\Models\Practitioner;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\API\BaseController as BaseController;

class EncounterController extends BaseController
{
    /**
     * 🔹 INDEX – Listar todos los encuentros paginados
     * Opcional: filtros por paciente, tipo y rango de fechas
     */
    public function index(Request $request): JsonResponse
    {
        $count = (int) ($request->_count ?? 10);

        $query = Encounter::with([
            'patient',
            'practitioner',
            'observations',
            'diagnosticReports'
        ])->orderBy('encounter_date', 'desc');

        // Filtros opcionales
        if ($request->has('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }
        if ($request->has('type')) {
            $query->where('encounter_type', $request->type);
        }
        if ($request->has('from')) {
            $query->where('encounter_date', '>=', $request->from);
        }
        if ($request->has('to')) {
            $query->where('encounter_date', '<=', $request->to);
        }

        $encounters = $query->paginate($count);

        return $this->sendResponse([
            'data' => $encounters->items(),
            'meta' => [
                'current_page' => $encounters->currentPage(),
                'last_page' => $encounters->lastPage(),
                'per_page' => $encounters->perPage(),
                'total' => $encounters->total(),
            ]
        ], 'Encuentros obtenidos correctamente');
    }

    /**
     * 🔹 STORE – Crear nuevo encounter
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'patient_id'      => 'required|exists:patients,id',
            'practitioner_id' => 'required|exists:practitioners,id',
            'organization_id' => 'nullable|exists:organizations,id',
            'encounter_date'  => 'required|date',
            'encounter_type'  => 'required|string',
            'status'          => 'required|in:planned,in-progress,onhold,finished,cancelled',
            'reason'          => 'nullable|string',
        ]);

        // Validar practitioner activo
        $practitioner = Practitioner::find($validated['practitioner_id']);
        if (!$practitioner || !$practitioner->active) {
            return $this->sendError('Profesional no activo', [], 404);
        }

        // Crear encounter
        $encounter = Encounter::create($validated);

        // Emitir evento encounter.created (opcional)
        // event(new EncounterCreated($encounter));

        return $this->sendResponse($encounter, 'Encuentro creado correctamente', 201);
    }

    /**
     * 🔹 SHOW – Detalle de encounter con relaciones
     */
    public function show(Encounter $encounter): JsonResponse
    {
        $encounter->load(['patient', 'practitioner', 'observations', 'diagnosticReports']);
        return $this->sendResponse($encounter, 'Detalle de encuentro obtenido');
    }

    /**
     * 🔹 UPDATE – Actualización parcial con PATCH
     */
    public function update(Request $request, Encounter $encounter): JsonResponse
    {
        if ($encounter->status === 'finished') {
            return $this->sendError('Encuentro finalizado no editable', [], 409);
        }

        $validated = $request->validate([
            'patient_id'      => 'sometimes|exists:patients,id',
            'practitioner_id' => 'sometimes|exists:practitioners,id',
            'organization_id' => 'sometimes|exists:organizations,id',
            'encounter_date'  => 'sometimes|date',
            'encounter_type'  => 'sometimes|string',
            'status'          => 'sometimes|in:planned,in-progress,onhold,finished,cancelled',
            'reason'          => 'nullable|string',
        ]);

        if (isset($validated['practitioner_id'])) {
            $practitioner = Practitioner::find($validated['practitioner_id']);
            if (!$practitioner || !$practitioner->active) {
                return $this->sendError('Profesional no activo', [], 404);
            }
        }

        $encounter->update($validated);

        return $this->sendResponse($encounter, 'Encuentro actualizado correctamente');
    }

    /**
     * 🔹 DESTROY – Eliminar encounter
     */
    public function destroy(Encounter $encounter): JsonResponse
    {
        $encounter->delete();
        return $this->sendResponse([], 'Encuentro eliminado correctamente');
    }

    /**
     * 🔹 FILTER – Método adicional si quieres filtrar en otra ruta
     */
    public function filter(Request $request): JsonResponse
    {
        $count = (int) ($request->_count ?? 10);

        $query = Encounter::with([
            'patient', 'practitioner', 'observations', 'diagnosticReports'
        ]);

        if ($request->has('patient_id')) $query->where('patient_id', $request->patient_id);
        if ($request->has('type')) $query->where('encounter_type', $request->type);
        if ($request->has('from')) $query->where('encounter_date', '>=', $request->from);
        if ($request->has('to')) $query->where('encounter_date', '<=', $request->to);

        $encounters = $query->orderBy('encounter_date', 'desc')->paginate($count);

        return $this->sendResponse([
            'data' => $encounters->items(),
            'meta' => [
                'current_page' => $encounters->currentPage(),
                'last_page' => $encounters->lastPage(),
                'per_page' => $encounters->perPage(),
                'total' => $encounters->total(),
            ]
        ], 'Encuentros filtrados correctamente');
    }
}
