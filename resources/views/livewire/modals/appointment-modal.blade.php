<div>
    <form wire:submit.prevent="save">
        <div class="space-y-4">
            <!-- Form inputs for appointment -->
            <select wire:model="patient_id" class="form-select">
                <option value="">Select Patient</option>
                @foreach ($patients as $patient)
                    <option value="{{ $patient->id }}">{{ $patient->full_name }}</option>
                @endforeach
            </select>
            <input type="datetime-local" wire:model="appointment_date" placeholder="Appointment Date" class="form-input">
            <textarea wire:model="reason" placeholder="Reason" class="form-input"></textarea>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Save</button>
            <button type="button" wire:click="$emit('closeModal')" class="btn btn-secondary">Cancel</button>
        </div>
    </form>
</div>
