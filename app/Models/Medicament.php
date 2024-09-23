<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicament extends Model
{
    use HasFactory;

    protected $table = 'medicament';

    protected $primaryKey = 'num_enr';  // The key in your Medicament table

    public $incrementing = false;  // Assuming 'num_enr' is not an auto-incrementing key

    protected $fillable = ['num_enr', 'nom_com', 'nom_dci', 'dosage', 'unite'];

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class, 'medicament_num_enr', 'num_enr');
    }
}

