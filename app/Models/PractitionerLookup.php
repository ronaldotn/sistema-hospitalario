<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PractitionerLookup extends Model
{
    protected $table = 'vw_practitioners_lookup';
    public $timestamps = false; // normalmente las vistas no tienen timestamps

    // Solo lectura: opcional si quieres protegerlo de inserts/updates
    protected $guarded = [];
}
