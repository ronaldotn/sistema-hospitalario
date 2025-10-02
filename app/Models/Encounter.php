<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Encounter extends Model
{
    /** @use HasFactory<\Database\Factories\EncounterFactory> */
    use HasFactory;

    public $timestamps = false;
    
    protected $fillable = [
        'patient_id',
        'practitioner_id',
        'organization_id',
        'encounter_date',
        'encounter_type',
        'reason',
        'status',
    ];

    protected $casts = [
        'encounter_date' => 'datetime',
    ];

    // Relaciones
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function practitioner()
    {
        return $this->belongsTo(Practitioner::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function conditions()
    {
        return $this->hasMany(Condition::class);
    }

    public function observations()
    {
        return $this->hasMany(Observation::class);
    }

    public function diagnosticReports()
    {
        return $this->hasMany(DiagnosticReport::class);
    }

    // Scopes
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in-progress');
    }

    public function scopeFinished($query)
    {
        return $query->where('status', 'finished');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('encounter_type', $type);
    }

    public function scopeConsultations($query)
    {
        return $query->where('encounter_type', 'consulta');
    }

    public function scopeEmergencies($query)
    {
        return $query->where('encounter_type', 'emergencia');
    }

    public function scopeHospitalizations($query)
    {
        return $query->where('encounter_type', 'hospitalización');
    }

    public function scopeForPatient($query, $patientId)
    {
        return $query->where('patient_id', $patientId);
    }

    public function scopeByPractitioner($query, $practitionerId)
    {
        return $query->where('practitioner_id', $practitionerId);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('encounter_date', '>=', now()->subDays($days));
    }
}
