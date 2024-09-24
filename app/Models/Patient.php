<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Patient extends Model
{
    protected $table = 'patient';
    protected $fillable = [
        'first_name', 'last_name', 'date_of_birth', 'gender', 'address', 'phone_number', 'email', 'medical_history'
    ];

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    // Relationship with visits
    public function visits()
    {
        return $this->hasMany(Visit::class);
    }

    // Relationship with appointments
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    // Cache Patient data with visits and appointments
    public static function getPatientWithRelations($patientId)
    {
        return Cache::remember("patient_{$patientId}_with_relations", 28800, function () use ($patientId) {
            return self::with(['visits', 'appointments'])->find($patientId);
        });
    }

    protected static function booted()
    {
        static::updated(function ($patient) {
            Cache::forget("patient_{$patient->id}_with_relations");
        });

        static::deleted(function ($patient) {
            Cache::forget("patient_{$patient->id}_with_relations");
        });
    }
}
