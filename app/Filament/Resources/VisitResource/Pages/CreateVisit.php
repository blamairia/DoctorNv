<?php
namespace App\Filament\Resources\VisitResource\Pages;

use App\Filament\Resources\VisitResource;
use App\Models\Visit;
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

                Action::make('addAppointment')
                    ->label('Add Appointment')
                    ->modalHeading('Add Appointment')
                    ->modalSubheading('Fill in the details for the appointment')
                    ->form(function () {
                        // Use a default or null check
                        $visit = $this->record ?? new Visit(); // Default to a new Visit if record is null

                        return [
                            Select::make('patient_id')
                                ->label('Patient')
                                ->options(Patient::all()->mapWithKeys(function ($patient) {
                                    return [$patient->id => "{$patient->first_name} {$patient->last_name}"];
                                }))
                                ->default($visit->patient_id ?? null) // Handle null
                                ->required(),

                            DateTimePicker::make('appointment_date')
                                ->label('Appointment Date')
                                ->default($visit->follow_up_date ?? now()) // Default to now if null
                                ->required(),

                            TextInput::make('reason')
                                ->label('Reason')
                                ->nullable()
                                ->maxLength(255),
                        ];
                    })
                    ->action(function (array $data) {
                        if (!empty($data['patient_id']) && !empty($data['appointment_date'])) {
                            Appointment::create($data); // Save the new appointment
                        } else {
                            // Handle error: show a notification
                            \Filament\Notifications\Notification::make()
                                ->title('Error')
                                ->body('Please fill in all required fields.')
                                ->success();
                        }
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
