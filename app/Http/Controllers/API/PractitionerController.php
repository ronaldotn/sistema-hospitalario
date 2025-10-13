<?php

namespace App\Http\Controllers\API;

use Faker\Provider\Base;
use App\Models\Practitioner;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\PractitionerLookup;
use App\Http\Controllers\API\BaseController as BaseController;

class PractitionerController extends BaseController
{
    /**
     * ======================================
     * 🔹 INDEX – Obtener lista de profesionales
     * ======================================
     * Este método entrega la lista de profesionales paginada directamente.
     * Laravel Eloquent con paginate() se encarga automáticamente de:
     * - Conteo total de registros
     * - Número de páginas
     * - Offset y página actual
     *
     * El frontend recibe únicamente la colección lista para renderizar.
     *
     * Query Params disponibles:
     * - first_name: filtra por nombre (like)
     * - last_name: filtra por apellido (like)
     * - active: filtra por estado (true/false)
     * - _count: cantidad de registros por página (default: 10)
     * - _page: número de página (default: 1, manejado automáticamente por paginate)
     *
     * 🔮 Espacio futuro:
     * - specialty, organization_id, rangos de fechas, búsqueda avanzada.
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
        $query = Practitioner::query();

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
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        // Validaciones
        $validated = $request->validate([
            'identifier'      => 'required|string|unique:practitioners,identifier',
            'first_name'      => 'required|string|max:255',
            'last_name'       => 'required|string|max:255',
            'specialty'       => 'required|string|max:255',
            'email'           => 'nullable|email|unique:practitioners,email',
            'phone'           => 'nullable|string|max:20',
            // 'organization_id' => 'nullable|exists:organizations,id',
            'active'          => 'required',
        ]);

        $practitioner = Practitioner::create($validated);

        return $this->sendResponse($practitioner, 'Profesional creado correctamente');
    }


    /**
     * Display the specified resource.
     */
    public function show(Practitioner $practitioner)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Practitioner $practitioner)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Practitioner $practitioner)
    {
        //
    }

    /**
     * Endpoint para obtener datos mínimos de Practicantes.
     * Se utiliza para seleccionar un profesional en un formulario de registro relacional (e.g., Citas).
     */
   public function lookup(Request $request): JsonResponse
{
    $search = trim($request->input('search', ''));

    $query = PractitionerLookup::query()
        ->when($search, fn($q) => $q->where('full_name', 'like', "%{$search}%"))
        ->orderBy('full_name')
        ->limit(20)
        ->get();

    return $this->sendResponse($query, 'Practicantes filtrados para selección rápida.');
}
}
