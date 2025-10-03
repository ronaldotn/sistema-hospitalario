<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Patient extends Model
{
    /** @use HasFactory<\Database\Factories\PractitionerFactory> */
    use HasFactory;

    /**
     * Los atributos que se pueden asignar masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'identifier',
        'first_name',
        'last_name',
        'date_of_birth',
        'gender',
        'phone',
        'email',
        'address',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // 🔹 Relaciones del modelo Patient

    /**
     * Un paciente puede tener muchos encuentros clínicos
     * (visitas médicas, consultas, hospitalizaciones, etc.).
     */
    public function encounters()
    {
        return $this->hasMany(Encounter::class);
    }

    /**
     * Un paciente puede tener muchas condiciones médicas
     * (enfermedades crónicas, diagnósticos registrados, etc.).
     */
    public function conditions()
    {
        return $this->hasMany(Condition::class);
    }

    /**
     * Un paciente puede tener muchas observaciones
     * (signos vitales, resultados de laboratorio simples, notas médicas).
     */
    public function observations()
    {
        return $this->hasMany(Observation::class);
    }

    /**
     * Un paciente puede tener muchos informes diagnósticos
     * (ejemplo: estudios de rayos X, resonancias, análisis de laboratorio completos).
     */
    public function diagnosticReports()
    {
        return $this->hasMany(DiagnosticReport::class);
    }

    /**
     * Un paciente puede tener muchos consentimientos informados
     * (autorizaciones de cirugías, tratamientos, compartir información médica).
     */
    public function consents()
    {
        return $this->hasMany(Consent::class);
    }


    // Accessors
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getAgeAttribute()
    {
        return $this->date_of_birth ? $this->date_of_birth->age : null;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereNotNull('email');
    }

    public function scopeByGender($query, $gender)
    {
        return $query->where('gender', $gender);
    }
}
