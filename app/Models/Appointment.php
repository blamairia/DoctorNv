<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Appointment extends Model
{
    protected $table = 'appointment';
    protected $fillable = ['patient_id', 'appointment_date', 'reason'];

    // Relationship with Patient
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    // Cache Appointments for a specific patient
    public static function getAppointmentsForPatient($patientId)
    {
        return Cache::remember("appointments_for_patient_{$patientId}", 28800, function () use ($patientId) {
            return Appointment::where('patient_id', $patientId)->get();
        });
    }

    protected static function booted()
    {
        static::created(function ($appointment) {
            Cache::forget("appointments_for_patient_{$appointment->patient_id}");
        });

        static::updated(function ($appointment) {
            Cache::forget("appointments_for_patient_{$appointment->patient_id}");
        });

        static::deleted(function ($appointment) {
            Cache::forget("appointments_for_patient_{$appointment->patient_id}");
        });
    }
}
