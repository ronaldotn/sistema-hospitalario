<?php

namespace App\Http\Controllers\API;



use App\Models\AuditEvents;
use App\Models\Practitioner;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class PractitionerController extends BaseController
{
    public function index(): JsonResponse
    {
        $practitioners = Practitioner::with('roles')->get();
        return $this->sendResponse($practitioners, 'Lista de profesionales', 200);
    }

    public function store(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'especialidad' => 'nullable|string|max:255',
            'nro_colegiado' => 'required|string|unique:practitioners,nro_colegiado',
            'email' => 'required|email|unique:practitioners,email',
            'telefono' => 'nullable|string|max:255',
            'estado' => 'nullable|in:activo,inactivo'
        ]);

      // El UUID se genera automáticamente en el modelo
    $practitioner = Practitioner::create($validatedData);

      
        // 🔒 Auditoría
        AuditEvents::create([
            'evento' => 'create_practitioner',
            'recurso' => 'practitioner',
            'recurso_uuid' => $practitioner->uuid,
            'detalle' => $validatedData,
            'usuario_id' => Auth::id() ?? null
        ]);

        return $this->sendResponse($practitioner, 'Profesional registrado exitosamente', 201);
    }

    public function show(string $uuid): JsonResponse
    {
        $practitioner = Practitioner::with('roles')->where('uuid', $uuid)->first();
        if (!$practitioner) return $this->sendError('Profesional no encontrado', [], 404);

        return $this->sendResponse($practitioner, 'Profesional encontrado', 200);
    }

    public function update(Request $request, string $uuid): JsonResponse
    {
        $practitioner = Practitioner::where('uuid', $uuid)->first();
        if (!$practitioner) return $this->sendError('Profesional no encontrado', [], 404);

        $validatedData = $request->validate([
            'nombre' => 'sometimes|required|string|max:255',
            'apellidos' => 'sometimes|required|string|max:255',
            'especialidad' => 'nullable|string|max:255',
            'nro_colegiado' => "sometimes|required|string|unique:practitioners,nro_colegiado,{$practitioner->id}",
            'email' => "sometimes|required|email|unique:practitioners,email,{$practitioner->id}",
            'telefono' => 'nullable|string|max:255',
            'estado' => 'nullable|in:activo,inactivo'
        ]);

        $practitioner->update($validatedData);
             // 🔒 Auditoría
        AuditEvents::create([
            'evento' => 'update_practitioner',
            'recurso' => 'practitioner',
            'recurso_uuid' => $practitioner->uuid,
            'detalle' => $validatedData,
            'usuario_id' => Auth::id() ?? null
        ]);

        return $this->sendResponse($practitioner, 'Profesional actualizado', 200);
    }

    public function destroy(string $uuid): JsonResponse
    {
        $practitioner = Practitioner::where('uuid', $uuid)->first();
        if (!$practitioner) return $this->sendError('Profesional no encontrado', [], 404);

        $practitioner->delete();
         // 🔒 Auditoría
        AuditEvents::create([
            'evento' => 'delete_practitioner',
            'recurso' => 'practitioner',
            'recurso_uuid' => $uuid,
            'detalle' => [],
            'usuario_id' => Auth::id() ?? null
        ]);
        return $this->sendResponse([], 'Profesional eliminado exitosamente', 200);
    }
}
