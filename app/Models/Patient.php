<?php

namespace App\Models;

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
}
