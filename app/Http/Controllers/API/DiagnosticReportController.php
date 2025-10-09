<?php

namespace App\Http\Controllers\API;

use App\Models\DiagnosticReport;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DiagnosticReportController extends BaseController
{
    /**
     * 🔹 INDEX – Listar todos los DiagnosticReports paginados
     * 🔹 Filtros opcionales que se pueden usar en reportes:
     *   - patient_id
     *   - encounter_id
     *   - type (tipo de informe)
     *   - status (final, provisional, etc. desde el JSON)
     *   - category (laboratory, radiology, etc. desde el JSON)
     *   - from / to (fechas de creación)
     */
    public function index(Request $request): JsonResponse
    {
        $count = (int) ($request->_count ?? 10);

        // Query base con relaciones
        $query = DiagnosticReport::with(['patient', 'encounter'])
            ->orderBy('created_at', 'desc');

        // 🔹 Filtros aplicables
        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }

        if ($request->filled('encounter_id')) {
            $query->where('encounter_id', $request->encounter_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            // Filtra por status dentro del JSON
            $query->whereJsonContains('document->status', $request->status);
        }

        if ($request->filled('category')) {
            // Filtra por category dentro del JSON
            $query->whereJsonContains('document->category', $request->category);
        }

        if ($request->filled('from')) {
            $query->where('created_at', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->where('created_at', '<=', $request->to);
        }

        // Paginación
        $reports = $query->paginate($count);

        return $this->sendResponse([
            'data' => $reports->items(),
            'meta' => [
                'current_page' => $reports->currentPage(),
                'last_page' => $reports->lastPage(),
                'per_page' => $reports->perPage(),
                'total' => $reports->total(),
            ]
        ], 'DiagnosticReports obtenidos correctamente');
    }

    /**
     * 🔹 STORE – Crear un nuevo DiagnosticReport
     * 🔹 Se valida que patient y encounter existan
     * 🔹 document debe ser un JSON válido según FHIR
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'patient_id'   => 'required|exists:patients,id',
            'encounter_id' => 'required|exists:encounters,id',
            'type'         => 'nullable|string|max:100',
            'result'       => 'nullable|string',
            'document'     => 'nullable|json', // JSON FHIR
        ]);

        $report = DiagnosticReport::create($validated);

        return $this->sendResponse($report, 'DiagnosticReport creado correctamente', 201);
    }

    /**
     * 🔹 SHOW – Mostrar detalle de un DiagnosticReport
     * 🔹 Se cargan relaciones: patient y encounter
     * 🔹 Los accessors del modelo permiten acceder a:
     *   - $report->status
     *   - $report->category
     *   - $report->conclusion
     */
    public function show(DiagnosticReport $diagnosticReport): JsonResponse
    {
        $diagnosticReport->load(['patient', 'encounter']);
        return $this->sendResponse($diagnosticReport, 'Detalle obtenido correctamente');
    }

    /**
     * 🔹 UPDATE – Actualización parcial con PATCH
     * 🔹 Solo se permite actualizar campos válidos de la tabla
     * 🔹 document se mantiene como JSON y puede actualizarse parcialmente
     */
    public function update(Request $request, DiagnosticReport $diagnosticReport): JsonResponse
    {
        $validated = $request->validate([
            'patient_id'   => 'sometimes|exists:patients,id',
            'encounter_id' => 'sometimes|exists:encounters,id',
            'type'         => 'sometimes|string|max:100',
            'result'       => 'sometimes|string',
            'document'     => 'sometimes|json',
        ]);

        $diagnosticReport->update($validated);

        return $this->sendResponse($diagnosticReport, 'DiagnosticReport actualizado correctamente');
    }

    /**
     * 🔹 DESTROY – Eliminar un DiagnosticReport
     */
    public function destroy(DiagnosticReport $diagnosticReport): JsonResponse
    {
        $diagnosticReport->delete();
        return $this->sendResponse([], 'DiagnosticReport eliminado correctamente');
    }

    /**
     * 🔹 FILTER – Método adicional si deseas endpoints separados para filtros
     * 🔹 Permite combinar filtros avanzados como reportes por rango de fechas, categoría, tipo, paciente o encounter
     */
    public function filter(Request $request): JsonResponse
    {
        $count = (int) ($request->_count ?? 10);

        $query = DiagnosticReport::with(['patient', 'encounter'])->orderBy('created_at', 'desc');

        if ($request->filled('patient_id')) $query->where('patient_id', $request->patient_id);
        if ($request->filled('encounter_id')) $query->where('encounter_id', $request->encounter_id);
        if ($request->filled('type')) $query->where('type', $request->type);
        if ($request->filled('status')) $query->whereJsonContains('document->status', $request->status);
        if ($request->filled('category')) $query->whereJsonContains('document->category', $request->category);
        if ($request->filled('from')) $query->where('created_at', '>=', $request->from);
        if ($request->filled('to')) $query->where('created_at', '<=', $request->to);

        $reports = $query->paginate($count);

        return $this->sendResponse([
            'data' => $reports->items(),
            'meta' => [
                'current_page' => $reports->currentPage(),
                'last_page' => $reports->lastPage(),
                'per_page' => $reports->perPage(),
                'total' => $reports->total(),
            ]
        ], 'DiagnosticReports filtrados correctamente');
    }
}
