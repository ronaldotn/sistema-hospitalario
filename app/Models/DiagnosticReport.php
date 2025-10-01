<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiagnosticReport extends Model
{
    /** @use HasFactory<\Database\Factories\DiagnosticReportFactory> */
    use HasFactory;

    public $timestamps = false;
    
    protected $fillable = [
        'patient_id',
        'encounter_id',
        'type',
        'result',
        'document',
    ];

    protected $casts = [
        'document' => 'array',
        'created_at' => 'datetime',
    ];

    // Relaciones
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'patient_id');
    }

    public function encounter()
    {
        return $this->belongsTo(Encounter::class, 'encounter_id', 'encounter_id');
    }

    // Scopes
    public function scopeForPatient($query, $patientId)
    {
        return $query->where('patient_id', $patientId);
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeLaboratory($query)
    {
        return $query->where('type', 'LIKE', '%Análisis%')
                     ->orWhere('type', 'LIKE', '%Lab%');
    }

    public function scopeRadiology($query)
    {
        return $query->whereIn('type', ['Radiografía', 'Tomografía', 'Resonancia']);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // Accessors
    public function getCategoryAttribute()
    {
        if (!$this->document || !isset($this->document['category'])) {
            return null;
        }
        return $this->document['category'];
    }

    public function getStatusAttribute()
    {
        if (!$this->document || !isset($this->document['status'])) {
            return null;
        }
        return $this->document['status'];
    }

    public function getConclusionAttribute()
    {
        if (!$this->document || !isset($this->document['conclusion'])) {
            return $this->result;
        }
        return $this->document['conclusion'];
    }
}
