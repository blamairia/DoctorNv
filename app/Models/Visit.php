<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Visit extends Model
{
    use HasFactory;

    protected $table = 'visit';
    protected $fillable = [
        'patient_id',
        'appointment_id',
        'visit_date',
        'notes',
        'diagnosis',
        'follow_up_date',
        'blood_work_diagnostics', // New field for blood work diagnostics
        'mri_scans',              // New field for MRI scans
        'xray_scans'              // New field for X-ray scans
    ];

    // Relationship with Patient
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    // Relationship with prescriptions
    public function prescriptions()
    {
        return $this->hasMany(Prescription::class, 'visit_id');
    }

    // Relationship with Appointment
    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    // Cache Visits for a specific patient
    public static function getVisitsForPatient($patientId)
    {
        return Cache::remember("visits_for_patient_{$patientId}", 28800, function () use ($patientId) {
            return Visit::where('patient_id', $patientId)->get();
        });
    }

    protected static function booted()
    {
        static::created(function ($visit) {
            Cache::forget("visits_for_patient_{$visit->patient_id}");
        });

        static::updated(function ($visit) {
            Cache::forget("visits_for_patient_{$visit->patient_id}");
        });

        static::deleted(function ($visit) {
            Cache::forget("visits_for_patient_{$visit->patient_id}");
        });
    }
}
