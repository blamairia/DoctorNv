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
use Illuminate\Support\Facades\Request;

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



            // Add Appointment Modal
            Action::make('addAppointment')
                ->label('Add Appointment')
                ->button()
                ->modalHeading('Add Appointment')
                ->modalSubheading('Fill in the details to add a new appointment')
                ->form([
                    Select::make('patient_id')
                        ->label('Patient')
                        ->options(Patient::all()->pluck('first_name', 'id')) // Use first and last name as necessary
                        ->required(),
                    DateTimePicker::make('appointment_date')->label('Appointment Date')->required(),
                ])
                ->action(function (array $data) {
                    Appointment::create($data);  // Logic to create a new appointment
                }),


        ];
    }
    protected function mutateFormDataBeforeCreate(array $data): array
        {
            // Ensure patient_id is set and passed, and default visit_date is handled
            $data['patient_id'] = $data['patient_id'] ?? Request::query('patient_id');
            $data['visit_date'] = $data['visit_date'] ?? Request::query('visit_date', now());

            return $data;
        }
    public function save()
    {
         // Ensure that the patient ID is included
         if (!isset($this->data['patient_id']) || !$this->data['patient_id']) {
            $this->notify('danger', 'Patient is required.');
            return;
        }


        // Open modal when follow-up date is present
        if ($this->data['appointment_modal']) {
            $this->dispatchBrowserEvent('open-add-appointment-modal');
        }
        parent::save();
    }

}
