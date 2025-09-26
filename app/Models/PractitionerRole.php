<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class PractitionerRole extends Model
{
 protected $fillable = [
        'uuid',
        'practitioner_id',
        'rol',
        'organizacion_id',
        'permisos',
    ];

    // 🔑 Cast para la columna JSON
    protected $casts = [
        'permisos' => 'array',
    ];

    /**
     * Generar automáticamente un UUID en la columna 'uuid'
     * cada vez que se crea un nuevo registro.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    // Relación inversa con Practitioner
    public function practitioner()
    {
        return $this->belongsTo(Practitioner::class);
    }
}
