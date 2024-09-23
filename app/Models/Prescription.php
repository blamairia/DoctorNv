<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    use HasFactory;

    protected $table = 'prescription'; // Ensure that the correct table name is used

    protected $fillable = [
        'medicament_num_enr', 'dosage_instructions', 'quantity', 'visit_id',
    ];

    public function visit()
    {
        return $this->belongsTo(Visit::class, 'visit_id');
    }

    public function medicament()
    {
        return $this->belongsTo(Medicament::class, 'medicament_num_enr', 'num_enr');
    }
}
