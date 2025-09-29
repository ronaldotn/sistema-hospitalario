<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    /**
     * Atributos asignables en masa.
     */
    protected $fillable = [
        'uuid',
        'nombre',
        'apellidos',
        'documento_identidad',
        'fecha_nacimiento',
        'sexo',
        'direccion',
        'contacto',
        'correo',
        'fhir_identifier',
        'version',
    ];

    /**
     * Conversión de campos a tipos nativos.
     */
    protected $casts = [
        'fhir_identifier' => 'array',
    ];

    /**
     * Genera automáticamente UUID al crear.
     */
    protected static function booted()
    {
        static::creating(function ($patient) {
            $patient->uuid = (string) Str::uuid();
        });
    }

    /**
     * Accessor: calcula edad en años.
     */
    public function getEdadAttribute(): ?int
    {
        return $this->fecha_nacimiento
            ? Carbon::parse($this->fecha_nacimiento)->age
            : null;
    }

    /**
     * Consulta con filtros, paginación y edad calculada.
     *
     * @param  array{identifier?:string,name?:string,_count?:int,_offset?:int} $filters
     * @return array{items:\Illuminate\Support\Collection,total:int}
     */
    public static function searchWithAge(array $filters): array
    {
        $perPage = $filters['_count'] ?? 20;
        $page    = $filters['_offset'] ?? 0;

        $query = self::query();

        // 🔹 Filtro por documento
        if (!empty($filters['identifier'])) {
            $query->where('documento_identidad', $filters['identifier']);
        }

        // 🔹 Filtro por nombre o apellidos
        if (!empty($filters['name'])) {
            $name = $filters['name'];
            $query->where(function ($q) use ($name) {
                $q->where('nombre', 'like', "%$name%")
                    ->orWhere('apellidos', 'like', "%$name%");
            });
        }

        $total = $query->count();

        // 🔹 Añade edad a cada registro
        $items = $query->skip($page)
            ->take($perPage)
            ->get()
            ->map(function ($p) {
                $p->edad = $p->edad; // usa accessor
                return $p;
            });

        return ['items' => $items, 'total' => $total];
    }
}
