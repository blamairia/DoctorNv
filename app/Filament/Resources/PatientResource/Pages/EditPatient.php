<?php
namespace App\Filament\Resources\PatientResource\Pages;

use App\Filament\Resources\PatientResource;
use App\Filament\Resources\VisitResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Pages\Actions\Action;
use Illuminate\Support\Carbon;

class EditPatient extends EditRecord
{
    protected static string $resource = PatientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Redirect to Add Visit Page
            Action::make('addVisit')
                ->label('Add Visit')
                ->url(fn () => VisitResource::getUrl('create', [
                    'patient_id' => $this->record->id,  // Prepopulate the selected patient
                    'visit_date' => now()->toDateTimeString(),  // Prepopulate visit date with current date and time
                ]))
                ->button(),
        ];
    }
}
