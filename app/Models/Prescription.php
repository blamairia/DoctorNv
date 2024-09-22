<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    protected $table = 'prescription';

    protected $fillable = ['visit_id', 'medicament_id', 'dosage_instructions', 'quantity'];

    // Define relationships
       public function visit()
    {
        return $this->belongsTo(Visit::class);
    }

    public function medicament()
    {
        return $this->belongsTo(Medicament::class);
    }
}
