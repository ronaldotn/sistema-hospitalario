<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Condition extends Model
{
    /** @use HasFactory<\Database\Factories\ConditionFactory> */
    use HasFactory;

    public $timestamps = false;
    
    protected $fillable = [
        'encounter_id',
        'patient_id',
        'code',
        'description',
    ];

    protected $casts = [
        'recorded_date' => 'datetime',
    ];

    // Relaciones
    public function encounter()
    {
        return $this->belongsTo(Encounter::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    // Scopes
    public function scopeForPatient($query, $patientId)
    {
        return $query->where('patient_id', $patientId);
    }

    public function scopeByCode($query, $code)
    {
        return $query->where('code', $code);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('recorded_date', '>=', now()->subDays($days));
    }

    // Métodos de utilidad
    public function isChronicDisease()
    {
        $chronicCodes = ['I10', 'E11.9', 'I20.0', 'I25.1', 'J44.9'];
        return in_array($this->code, $chronicCodes);
    }
}
