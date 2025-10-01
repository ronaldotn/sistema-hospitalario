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

    // Relaciones
    public function encounters()
    {
        return $this->hasMany(Encounter::class, 'patient_id', 'patient_id');
    }

    public function conditions()
    {
        return $this->hasMany(Condition::class, 'patient_id', 'patient_id');
    }

    public function observations()
    {
        return $this->hasMany(Observation::class, 'patient_id', 'patient_id');
    }

    public function diagnosticReports()
    {
        return $this->hasMany(DiagnosticReport::class, 'patient_id', 'patient_id');
    }

    public function consents()
    {
        return $this->hasMany(Consent::class, 'patient_id', 'patient_id');
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
