<?php
namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\Patient;

class PatientWidget extends Widget
{
    protected static string $view = 'filament.widgets.patient-widget';

    // Pass data to the view
    public function getViewData(): array
    {
        // Count the number of patients
        $patientCount = Patient::count();

        return [
            'count' => $patientCount,
        ];
    }
}
