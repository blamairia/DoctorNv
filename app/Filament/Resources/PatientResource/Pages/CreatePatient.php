<?php

namespace App\Filament\Resources\PatientResource\Pages;

use App\Filament\Resources\PatientResource;
use App\Filament\Resources\VisitResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Pages\Actions\Action;

class CreatePatient extends CreateRecord
{
    protected static string $resource = PatientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Redirect to Add Visit Page
            Action::make('addVisit')
                ->label('Add Visit')
                ->url(fn () => VisitResource::getUrl('create', [
                    'patient_id' => $this->record->id ?? null,  // Prepopulate patient ID if it exists
                    'visit_date' => now()->toDateTimeString(),   // Prepopulate visit date with current date and time
                ]))
                ->button(),
        ];
    }
}
