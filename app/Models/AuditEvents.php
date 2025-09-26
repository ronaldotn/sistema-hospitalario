<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class AuditEvents extends Model
{
      protected $fillable = [
        'uuid',
        'evento',
        'recurso',
        'recurso_uuid',
        'detalle',
        'usuario_id',
    ];

    protected $casts = [
        'detalle' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }
}
