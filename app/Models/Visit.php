<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    protected $table = 'visit';

    protected $fillable = ['patient_id', 'appointment_id', 'visit_date', 'notes', 'diagnosis', 'follow_up_date'];

    // Define relationships
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
    public function prescriptions()
    {
        // The relationship is with the 'prescription' table, not 'prescriptions'
        return $this->hasMany(Prescription::class, 'visit_id');
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
    use HasFactory;
}
