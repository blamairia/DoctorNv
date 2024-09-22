<?php
namespace App\Filament\Resources\VisitResource\Pages;

use App\Filament\Resources\VisitResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Pages\Actions\Action;

class CreateVisit extends CreateRecord
{
    protected static string $resource = VisitResource::class;

    protected function getActions(): array
    {
        return [
            Action::make('addPatient')
                ->label('Add Patient')
                ->button()
                ->icon('heroicon-o-pencil')  // Using a simple icon for all
                ->action('openAddPatientModal'),

            Action::make('editPatient')
                ->label('Edit Patient')
                ->button()
                ->icon('heroicon-o-pencil')  // Same icon for edit
                ->action('openEditPatientModal'),

            Action::make('addAppointment')
                ->label('Add Appointment')
                ->button()
                ->icon('heroicon-o-pencil')  // Same icon for appointment
                ->action('openAddAppointmentModal'),

            Action::make('editAppointment')
                ->label('Edit Appointment')
                ->button()
                ->icon('heroicon-o-pencil')  // Same icon for editing appointment
                ->action('openEditAppointmentModal'),
        ];
    }

    public function openAddPatientModal()
    {
        // Logic to open the Add Patient modal
        $this->dispatchBrowserEvent('open-add-patient-modal');
    }

    public function openEditPatientModal()
    {
        // Logic to open the Edit Patient modal
        $this->dispatchBrowserEvent('open-edit-patient-modal');
    }

    public function openAddAppointmentModal()
    {
        $this->dispatchBrowserEvent('open-add-appointment-modal');
    }

    public function openEditAppointmentModal()
    {
        $this->dispatchBrowserEvent('open-edit-appointment-modal');
    }
}

