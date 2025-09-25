<?php

namespace App\Models;

use Carbon\Carbon;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
     /**
     * Los atributos que se pueden asignar masivamente.
     *
     * @var array<int, string>
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
    ];

    /**
     * Los atributos que se deben convertir a tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'fhir_identifier' => 'array',
    ];

    /**
     * El método "booted" del modelo.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($patient) {
            $patient->uuid = (string) Str::uuid();
        });
    }
      // ✅ Accessor para calcular la edad automáticamente
    public function getEdadAttribute()
    {
        if (!$this->fecha_nacimiento) return null;
        return Carbon::parse($this->fecha_nacimiento)->age;
    }
        // ✅ Método para obtener todos los pacientes con edad
    public static function allWithAge()
    {
        return self::all()->map(function ($p) {
            $p->edad = $p->edad; // usa el accessor
            return $p;
        });
    }
}
