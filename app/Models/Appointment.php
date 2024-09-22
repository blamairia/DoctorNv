<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $table = 'appointment';

    protected $fillable = ['patient_id', 'appointment_date', 'reason'];

    // Define relationships
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}

