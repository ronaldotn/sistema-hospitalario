<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Observation extends Model
{
    /** @use HasFactory<\Database\Factories\ObservationFactory> */
    use HasFactory;

    public $timestamps = false;
    
    protected $fillable = [
        'encounter_id',
        'patient_id',
        'code',
        'value',
        'unit',
    ];

    protected $casts = [
        'observed_at' => 'datetime',
        'value' => 'string',
    ];

    // Relaciones
    public function encounter()
    {
        return $this->belongsTo(Encounter::class, 'encounter_id', 'encounter_id');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'patient_id');
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

    public function scopeVitalSigns($query)
    {
        return $query->whereIn('code', ['8480-6', '8462-4', '8867-4', '8310-5']);
    }

    public function scopeLabResults($query)
    {
        return $query->whereIn('code', ['2093-3', '2345-7', '718-7']);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('observed_at', '>=', now()->subDays($days));
    }

    // Accessors
    public function getFormattedValueAttribute()
    {
        return "{$this->value} {$this->unit}";
    }

    // Métodos de utilidad
    public function isAbnormal()
    {
        $normalRanges = [
            '8480-6' => ['min' => 90, 'max' => 140],   // Presión sistólica
            '8462-4' => ['min' => 60, 'max' => 90],    // Presión diastólica
            '8867-4' => ['min' => 60, 'max' => 100],   // Frecuencia cardíaca
            '8310-5' => ['min' => 36.0, 'max' => 37.5], // Temperatura
            '2093-3' => ['min' => 125, 'max' => 200],  // Colesterol
            '2345-7' => ['min' => 70, 'max' => 100],   // Glucosa
        ];

        if (!isset($normalRanges[$this->code])) {
            return false;
        }

        $range = $normalRanges[$this->code];
        $value = (float) $this->value;

        return $value < $range['min'] || $value > $range['max'];
    }
}
