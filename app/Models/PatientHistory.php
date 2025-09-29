<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientHistory extends Model
{
       protected $fillable = ['patient_uuid','version','data','updated_by'];
}
