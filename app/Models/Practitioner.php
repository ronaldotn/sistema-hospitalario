<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Practitioner extends Model
{
    /** @use HasFactory<\Database\Factories\PractitionerFactory> */
    use HasFactory;

    protected $fillable = [
        'identifier',
        'first_name',
        'last_name',
        'specialty',
        'phone',
        'email',
        'organization_id',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relaciones
    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id', 'organization_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'practitioner_id', 'practitioner_id');
    }

    public function encounters()
    {
        return $this->hasMany(Encounter::class, 'practitioner_id', 'practitioner_id');
    }

    // Accessors
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('active', false);
    }

    public function scopeBySpecialty($query, $specialty)
    {
        return $query->where('specialty', $specialty);
    }

    public function scopeInOrganization($query, $organizationId)
    {
        return $query->where('organization_id', $organizationId);
    }
}
