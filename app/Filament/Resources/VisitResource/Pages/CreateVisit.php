<?php

namespace App\Filament\Resources\VisitResource\Pages;

use App\Filament\Resources\VisitResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Pages\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DateTimePicker;

use App\Models\Appointment;

use App\Models\Patient;
class CreateVisit extends CreateRecord
{
    protected static string $resource = VisitResource::class;

    protected function getActions(): array
    {
        return [
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

            Action::make('editPatient')
                ->label('Edit Patient')
                ->modalHeading('Edit Patient')
                ->action(function (array $data) {
                    $patient = Patient::find($this->data['patient_id']);
                    if ($patient) {
                        $patient->update($data);
                    }
                })
                ->form(function () {
                    $patient = Patient::find($this->data['patient_id']);
                    return [
                        TextInput::make('first_name')->default($patient->first_name)->required(),
                        TextInput::make('last_name')->default($patient->last_name)->required(),
                        DatePicker::make('date_of_birth')->default($patient->date_of_birth)->required(),
                        Select::make('gender')
                            ->options(['Male' => 'Male', 'Female' => 'Female'])
                            ->default($patient->gender)
                            ->required(),
                        TextInput::make('address')->default($patient->address)->required(),
                        TextInput::make('phone_number')->default($patient->phone_number)->required(),
                        TextInput::make('email')->default($patient->email)->email(),
                    ];
                })
                ->button(),

            // Add Appointment Modal
            Action::make('addAppointment')
                ->label('Add Appointment')
                ->button()
                ->modalHeading('Add Appointment')
                ->modalSubheading('Fill in the details to add a new appointment')
                ->form([
                    Select::make('patient_id')
                        ->label('Patient')
                        ->options(Patient::all()->pluck('full_name', 'id')) // Ensure you have `first_name` and `last_name` concatenated
                        ->required(),
                    DateTimePicker::make('appointment_date')->label('Appointment Date')->required(),
                ])
                ->action(function (array $data) {
                    Appointment::create($data);  // Logic to create a new appointment
                }),

            Action::make('editAppointment')
                ->label('Edit Appointment')
                ->modalHeading('Edit Appointment')
                ->action(function (array $data) {
                    $appointment = Appointment::find($this->data['appointment_id']);
                    if ($appointment) {
                        $appointment->update($data);
                    }
                })
                ->form(function () {
                    $appointment = Appointment::find($this->data['appointment_id']);
                    return [
                        Select::make('patient_id')
                            ->label('Patient')
                            ->relationship('patient', 'full_name')
                            ->default($appointment->patient_id)
                            ->required(),
                        DateTimePicker::make('appointment_date')
                            ->default($appointment->appointment_date)
                            ->required(),
                        Textarea::make('reason')
                            ->default($appointment->reason)
                            ->nullable(),
                    ];
                })
                ->button(),
        ];
    }
}
