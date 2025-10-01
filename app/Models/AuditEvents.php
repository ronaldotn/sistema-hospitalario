<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditEvents extends Model
{
    /** @use HasFactory<\Database\Factories\AuditEventsFactory> */
    use HasFactory;

    public $timestamps = false;
    
    protected $fillable = [
        'user_id',
        'action',
        'resource',
        'details',
    ];

    protected $casts = [
        'details' => 'array',
        'timestamp' => 'datetime',
    ];

    // Relaciones
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    // Scopes
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    public function scopeByResource($query, $resource)
    {
        return $query->where('resource', 'LIKE', "%{$resource}%");
    }

    public function scopeLogins($query)
    {
        return $query->where('action', 'LOGIN');
    }

    public function scopeCreates($query)
    {
        return $query->where('action', 'CREATE');
    }

    public function scopeUpdates($query)
    {
        return $query->where('action', 'UPDATE');
    }

    public function scopeDeletes($query)
    {
        return $query->where('action', 'DELETE');
    }

    public function scopeViews($query)
    {
        return $query->where('action', 'VIEW');
    }

    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('timestamp', '>=', now()->subHours($hours));
    }

    public function scopeToday($query)
    {
        return $query->whereDate('timestamp', today());
    }

    // Métodos estáticos para logging
    public static function logLogin($userId, $ip, $userAgent)
    {
        return static::create([
            'user_id' => $userId,
            'action' => 'LOGIN',
            'resource' => 'system',
            'details' => [
                'ip' => $ip,
                'user_agent' => $userAgent,
            ],
        ]);
    }

    public static function logView($userId, $resource, $details = [])
    {
        return static::create([
            'user_id' => $userId,
            'action' => 'VIEW',
            'resource' => $resource,
            'details' => $details,
        ]);
    }

    public static function logCreate($userId, $resource, $details = [])
    {
        return static::create([
            'user_id' => $userId,
            'action' => 'CREATE',
            'resource' => $resource,
            'details' => $details,
        ]);
    }

    public static function logUpdate($userId, $resource, $details = [])
    {
        return static::create([
            'user_id' => $userId,
            'action' => 'UPDATE',
            'resource' => $resource,
            'details' => $details,
        ]);
    }

    public static function logDelete($userId, $resource, $details = [])
    {
        return static::create([
            'user_id' => $userId,
            'action' => 'DELETE',
            'resource' => $resource,
            'details' => $details,
        ]);
    }
}
