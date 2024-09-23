<?php


namespace App\Filament\Resources\VisitResource\Pages;

use App\Filament\Resources\VisitResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use App\Models\Patient;
use App\Models\Appointment;

class EditVisit extends EditRecord
{
    protected static string $resource = VisitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),

            // New Add Appointment Button
            Actions\Action::make('addAppointment')
                ->label('Add Appointment')
                ->modalHeading('Add Appointment')
                ->modalSubheading('Fill in the details for the appointment')
                ->form(function () {
                    $visit = $this->record; // Get the visit record
                    return [
                        Select::make('patient_id')
                            ->label('Patient')
                            ->options(Patient::all()->mapWithKeys(function ($patient) {
                                return [$patient->id => $patient->first_name . ' ' . $patient->last_name];
                            }))
                            ->default($visit->patient_id)
                            ->disabled(),

                        DateTimePicker::make('appointment_date')
                            ->label('Appointment Date')
                            ->default($visit->follow_up_date) // Preselect the follow-up date
                            ->required(),
                        Textarea::make('reason')
                            ->label('Reason')
                            ->nullable(),
                    ];
                })
                ->action(function (array $data) {
                    Appointment::create($data); // Logic to save the new appointment
                }),
        ];
    }
}
