<?php


namespace App\Filament\Resources\VisitResource\Pages;

use App\Filament\Resources\VisitResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
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

            Action::make('addPatient')
                ->label('Add Patient')
                ->modalHeading('Add Patient')
                ->action(function (array $data) {
                    $patient = Patient::create($data);
                    $this->fillForm(['patient_id' => $patient->id]); // Auto-select the newly created patient
                })
                ->form([
                    TextInput::make('first_name')->required(),
                    TextInput::make('last_name')->required(),
                    DatePicker::make('date_of_birth')->required(),
                    Select::make('gender')
                        ->options(['Male' => 'Male', 'Female' => 'Female'])
                        ->required(),
                    TextInput::make('address')->required(),
                    TextInput::make('phone_number')->required(),
                    TextInput::make('email')->email(),
                ])
                ->button(),
            // New Add Appointment Button
            Action::make('addAppointment')
                ->label('Add Appointment')
                ->modalHeading('Add Appointment')
                ->modalSubheading('Fill in the details for the appointment')
                ->form(function () {
                    $visit = $this->record; // Get the visit record
                    return [
                        Select::make('patient_id')
                            ->label('Patient')
                            ->options(Patient::all()->mapWithKeys(function ($patient) {
                                return [$patient->id => "{$patient->first_name} {$patient->last_name}"];
                            }))
                            ->default($visit->patient_id)
                            ->disabled(), // Disable if needed

                        DateTimePicker::make('appointment_date')
                            ->label('Appointment Date')
                            ->default($visit->follow_up_date) // Preselect follow-up date
                            ->required(),

                            TextInput::make('reason') // Use TextInput instead of Textarea
                            ->label('Reason')
                            ->nullable() // or required() if necessary
                            ->maxLength(255), // Optionally limit the length
                    ];
                })
                ->action(function (array $data) {
                    Appointment::create($data); // Logic to save the new appointment
                }),
            ];
    }
}
