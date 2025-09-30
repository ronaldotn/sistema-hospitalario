<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    /** @use HasFactory<\Database\Factories\OrganizationFactory> */
    use HasFactory;

    public $timestamps = false;
    
    protected $fillable = [
        'name',
        'type',
        'address',
        'phone',
        'email',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // Relaciones
    public function practitioners()
    {
        return $this->hasMany(Practitioner::class, 'organization_id', 'organization_id');
    }

    public function encounters()
    {
        return $this->hasMany(Encounter::class, 'organization_id', 'organization_id');
    }

    public function consents()
    {
        return $this->hasMany(Consent::class, 'granted_to', 'organization_id');
    }
}
