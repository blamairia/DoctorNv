<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Specialite extends Model
{
    protected $table = 'specialite';  // Reference the existing table
    protected $primaryKey = 'code_sp';  // Primary key of the table
    public $timestamps = false;  // Assuming no timestamp fields

    // Define the relationship with Medicament
    public function medicaments()
    {
        return $this->belongsToMany(Medicament::class, 'medicament_specialite', 'specialite_code_sp', 'medicament_num_enr');
    }
}
