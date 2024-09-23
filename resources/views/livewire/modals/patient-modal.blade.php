<div>
    <form wire:submit.prevent="save">
        <div class="space-y-4">
            <input type="text" wire:model="first_name" placeholder="First Name" class="form-input">
            @error('first_name') <span class="text-red-600">{{ $message }}</span> @enderror

            <input type="text" wire:model="last_name" placeholder="Last Name" class="form-input">
            @error('last_name') <span class="text-red-600">{{ $message }}</span> @enderror

            <input type="date" wire:model="date_of_birth" placeholder="Date of Birth" class="form-input">
            @error('date_of_birth') <span class="text-red-600">{{ $message }}</span> @enderror

            <select wire:model="gender" class="form-select">
                <option value="">Select Gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>
            @error('gender') <span class="text-red-600">{{ $message }}</span> @enderror

            <input type="text" wire:model="address" placeholder="Address" class="form-input">
            @error('address') <span class="text-red-600">{{ $message }}</span> @enderror

            <input type="text" wire:model="phone_number" placeholder="Phone Number" class="form-input">
            @error('phone_number') <span class="text-red-600">{{ $message }}</span> @enderror

            <input type="email" wire:model="email" placeholder="Email" class="form-input">
            @error('email') <span class="text-red-600">{{ $message }}</span> @enderror

        </div>
        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Save</button>
            <button type="button" wire:click="closeModal" class="btn btn-secondary">Cancel</button>
        </div>
    </form>
</div>
