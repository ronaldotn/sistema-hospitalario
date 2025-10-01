<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consent extends Model
{
    /** @use HasFactory<\Database\Factories\ConsentFactory> */
    use HasFactory;

    public $timestamps = false;
    
    protected $fillable = [
        'patient_id',
        'granted_to',
        'scope',
        'valid_from',
        'valid_until',
        'revoked',
    ];

    protected $casts = [
        'valid_from' => 'date',
        'valid_until' => 'date',
        'revoked' => 'boolean',
        'created_at' => 'datetime',
    ];

    // Relaciones
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'patient_id');
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'granted_to', 'organization_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('revoked', false)
                     ->where('valid_from', '<=', now())
                     ->where('valid_until', '>=', now());
    }

    public function scopeRevoked($query)
    {
        return $query->where('revoked', true);
    }

    public function scopeExpired($query)
    {
        return $query->where('valid_until', '<', now())
                     ->where('revoked', false);
    }

    public function scopeForPatient($query, $patientId)
    {
        return $query->where('patient_id', $patientId);
    }

    public function scopeForOrganization($query, $organizationId)
    {
        return $query->where('granted_to', $organizationId);
    }

    public function scopeFullAccess($query)
    {
        return $query->where('scope', 'completo');
    }

    public function scopePartialAccess($query)
    {
        return $query->where('scope', 'parcial');
    }

    // Métodos de utilidad
    public function isActive()
    {
        return !$this->revoked 
            && $this->valid_from <= now() 
            && $this->valid_until >= now();
    }

    public function isExpired()
    {
        return !$this->revoked && $this->valid_until < now();
    }

    public function revoke()
    {
        $this->update(['revoked' => true]);
    }

    public function daysUntilExpiration()
    {
        if ($this->revoked || $this->isExpired()) {
            return 0;
        }
        return now()->diffInDays($this->valid_until);
    }
}
