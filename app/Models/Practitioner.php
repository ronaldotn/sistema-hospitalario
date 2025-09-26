<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Practitioner extends Model
{
  
    protected $fillable = [
        'uuid',
        'nombre',
        'apellidos',
        'especialidad',
        'nro_colegiado',
        'email',
        'telefono',
        'estado',
    ];

    // 🔑 Generar uuid automático solo para la columna uuid
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    // Relaciones
    public function roles()
    {
        return $this->hasMany(PractitionerRole::class);
    }
}
