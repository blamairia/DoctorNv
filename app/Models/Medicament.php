<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

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

    // Cache Medicament data permanently
    public static function getMedicaments()
    {
        return Cache::rememberForever('medicaments_all', function () {
            return Medicament::all();  // Fetch all medicaments
        });
    }

    // Optionally, if Medicament data ever changes, manually clear the cache
    public static function clearMedicamentCache()
    {
        Cache::forget('medicaments_all');
    }
}
