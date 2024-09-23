<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    // Specify the correct table name
    protected $table = 'patient';

    // Add any necessary relationships and fields, e.g.
    protected $fillable = [
        'first_name', 'last_name', 'date_of_birth', 'gender', 'address', 'phone_number', 'email', 'medical_history'
    ];
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }
     // Add this visits relationship
     public function visits()
     {
         return $this->hasMany(Visit::class);
     }
       // Add the appointments relationship
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

}
