<?php

namespace App\Http\Controllers\API;

use App\Models\Consent;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\API\BaseController as BaseController;

class ConsentController extends BaseController
{
    /**
     * ======================================
     * 🔹 INDEX – Listar consentimientos
     * ======================================
     * Permite filtrar por paciente o por organización a quien se concede.
     * Query params disponibles:
     * - patient_id
     * - granted_to
     * - _count (default 10)
     */
    public function index(Request $request): JsonResponse
    {
        $count = (int) ($request->_count ?? 10);
        $patientId = $request->input('patient_id');
        $grantedTo = $request->input('granted_to');

        $query = Consent::query()->with(['patient', 'grantedTo']); // Eager load para evitar N+1

        if ($patientId) {
            $query->where('patient_id', $patientId);
        }

        if ($grantedTo) {
            $query->where('granted_to', $grantedTo);
        }

        $query->orderBy('valid_from', 'desc')
              ->orderBy('id', 'desc');

        $consents = $query->paginate($count);

        return $this->sendResponse($consents, 'Lista de consentimientos obtenida correctamente');
    }

    /**
     * ======================================
     * 🔹 STORE – Crear nuevo consentimiento
     * ======================================
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'patient_id'  => 'required|exists:patients,id',
            'granted_to'  => 'required|exists:organizations,id',
            'scope'       => 'nullable|string|max:100',
            'valid_from'  => 'nullable|date',
            'valid_until' => 'nullable|date|after_or_equal:valid_from',
            'revoked'     => 'nullable|boolean',
        ]);

        $consent = Consent::create($validated);

        // Cargar relaciones para la respuesta
        $consent->load('patient', 'grantedTo');

        return $this->sendResponse($consent, 'Consentimiento creado correctamente');
    }

    /**
     * ======================================
     * 🔹 SHOW – Mostrar un consentimiento específico
     * ======================================
     */
    public function show(Consent $consent): JsonResponse
    {
        // Eager load para evitar N+1
        $consent->load('patient', 'grantedTo');

        return $this->sendResponse($consent, 'Consentimiento obtenido correctamente');
    }

    /**
     * ======================================
     * 🔹 UPDATE – Actualizar un consentimiento
     * ======================================
     */
    public function update(Request $request, Consent $consent): JsonResponse
    {
        $validated = $request->validate([
            'patient_id'  => 'nullable|exists:patients,id',
            'granted_to'  => 'nullable|exists:organizations,id',
            'scope'       => 'nullable|string|max:100',
            'valid_from'  => 'nullable|date',
            'valid_until' => 'nullable|date|after_or_equal:valid_from',
            'revoked'     => 'nullable|boolean',
        ]);

        $consent->update($validated);

        // Cargar relaciones para la respuesta
        $consent->load('patient', 'grantedTo');

        return $this->sendResponse($consent, 'Consentimiento actualizado correctamente');
    }

    /**
     * ======================================
     * 🔹 DESTROY – Eliminar un consentimiento
     * ======================================
     */
    public function destroy(Consent $consent): JsonResponse
    {
        $consent->delete();

        return $this->sendResponse([], 'Consentimiento eliminado correctamente');
    }
}
