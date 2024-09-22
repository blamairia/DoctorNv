<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medicament extends Model
{
    protected $table = 'medicament';  // Reference the existing table
    protected $primaryKey = 'num_enr';  // Primary key of the table
    public $timestamps = false;  // Assuming you don't have `created_at` or `updated_at`

    // Define the relationship with Specialite
    public function specialites()
    {
        return $this->belongsToMany(Specialite::class, 'medicament_specialite', 'medicament_num_enr', 'specialite_code_sp');
    }
}
