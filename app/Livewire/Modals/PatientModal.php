<?php
namespace App\Http\Livewire\Modals;

use Livewire\Component;
use App\Models\Patient;

class PatientModal extends Component
{
    public $patient;
    public $first_name;
    public $last_name;
    public $date_of_birth;
    public $gender;
    public $address;
    public $phone_number;
    public $email;

    public function mount($patientId = null)
    {
        if ($patientId) {
            $this->patient = Patient::findOrFail($patientId);
            $this->first_name = $this->patient->first_name;
            $this->last_name = $this->patient->last_name;
            $this->date_of_birth = $this->patient->date_of_birth;
            $this->gender = $this->patient->gender;
            $this->address = $this->patient->address;
            $this->phone_number = $this->patient->phone_number;
            $this->email = $this->patient->email;
        }
    }

    public function save()
    {
        $this->validate([
            'first_name' => 'required|max:100',
            'last_name' => 'required|max:100',
            'date_of_birth' => 'required|date',
            'gender' => 'required',
            'address' => 'required|max:255',
            'phone_number' => 'required|max:15',
            'email' => 'nullable|email',
        ]);

        Patient::updateOrCreate(
            ['id' => $this->patient->id ?? null],
            [
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'date_of_birth' => $this->date_of_birth,
                'gender' => $this->gender,
                'address' => $this->address,
                'phone_number' => $this->phone_number,
                'email' => $this->email,
            ]
        );

        $this->emit('patientSaved');
        $this->closeModal();
    }

    public function closeModal()
    {
        $this->emit('closeModal');
    }

    public function render()
    {
        return view('livewire.modals.patient-modal');
    }
}
