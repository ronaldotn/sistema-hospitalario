<?php

namespace App\Http\Controllers\API;

use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\API\BaseController as BaseController;

class OrganizationController extends BaseController
{
    /**
     * ======================================
     * 🔹 INDEX – Obtener lista de organizaciones
     * ======================================
     * Lista todas las organizaciones paginadas.
     *
     * Query Params disponibles:
     * - name: filtra por nombre (like)
     * - type: filtra por tipo (hospital, laboratorio, farmacia)
     * - _count: cantidad de registros por página (default: 10)
     * - _page: número de página (default: 1, manejado automáticamente por paginate)
     *
     * 🔮 Espacio futuro:
     * - dirección, teléfono, email, búsquedas avanzadas
     */
    public function index(Request $request): JsonResponse
    {
        // ======================================
        // 🔹 0️⃣ Valores por defecto y seguridad
        // ======================================
        $count   = (int) ($request->_count ?? 10);
        $name    = trim($request->input('name', ''));
        $type    = trim($request->input('type', ''));

        // ======================================
        // 🔹 1️⃣ Construir query base
        // ======================================
        $query = Organization::query();

        // ======================================
        // 🔹 2️⃣ Aplicar filtros dinámicos (comentado por ahora)
        // ======================================
        // if (!empty($name)) {
        //     $query->where('name', 'like', "%{$name}%");
        // }
        //
        // if (!empty($type)) {
        //     $query->where('type', $type);
        // }

        // ======================================
        // 🔹 3️⃣ Ordenamiento jerárquico
        // ======================================
        // Por defecto, primero las más recientes
        $query->orderBy('created_at', 'desc')
            ->orderBy('name', 'asc'); // desempate alfabético

        // ======================================
        // 🔹 4️⃣ Paginación automática
        // ======================================
        $organizations = $query->paginate($count);

        // ======================================
        // 🔹 5️⃣ Devolver respuesta JSON
        // ======================================

        return $this->sendResponse($organizations, 'Lista de organizaciones filtrada y ordenada');
    }

    /**
     * ======================================
     * 🔹 STORE – Crear nueva organización
     * ======================================
     */
    public function store(Request $request): JsonResponse
    {
        // ======================================
        // 🔹 0️⃣ Validar datos
        // ======================================
        $validated = $request->validate([
            'name'    => 'required|string|max:200',
            'type'    => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'phone'   => 'nullable|string|max:20',
            'email'   => 'nullable|email|max:100',
        ]);

        // ======================================
        // 🔹 1️⃣ Crear organización
        // ======================================
        $organization = Organization::create($validated);

        // ======================================
        // 🔹 2️⃣ Respuesta
        // ======================================
        return response()->json([
            'success' => true,
            'data'    => $organization,
            'message' => 'Organización creada exitosamente'
        ], 201);
    }

    /**
     * ======================================
     * 🔹 SHOW – Mostrar detalle de organización
     * ======================================
     */
    public function show(Organization $organization): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => $organization,
            'message' => 'Detalle de la organización'
        ]);
    }

    /**
     * ======================================
     * 🔹 UPDATE – Actualizar organización existente
     * ======================================
     */
    public function update(Request $request, Organization $organization): JsonResponse
    {
        // ======================================
        // 🔹 0️⃣ Validar datos
        // ======================================
        $validated = $request->validate([
            'name'    => 'sometimes|required|string|max:200',
            'type'    => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'phone'   => 'nullable|string|max:20',
            'email'   => 'nullable|email|max:100',
        ]);

        // ======================================
        // 🔹 1️⃣ Actualizar
        // ======================================
        $organization->update($validated);

        // ======================================
        // 🔹 2️⃣ Respuesta
        // ======================================
        return $this->sendResponse($organization, 'Organización actualizada exitosamente');
    }

    /**
     * ======================================
     * 🔹 DESTROY – Eliminar organización
     * ======================================
     */
    public function destroy(Organization $organization): JsonResponse
    {
        $organization->delete();

        return response()->json([
            'success' => true,
            'message' => 'Organización eliminada exitosamente'
        ]);
    }
}
