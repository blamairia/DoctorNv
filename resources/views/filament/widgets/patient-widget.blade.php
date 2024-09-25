<x-filament::widget>
    <x-filament::card>
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-xl font-semibold">Patients</h2>
                <p>{{ $count }} patients registered</p>
            </div>
            <x-filament::button tag="a" href="{{ \App\Filament\Resources\PatientResource::getUrl('index') }}">
                View Patients
            </x-filament::button>
        </div>
    </x-filament::card>
</x-filament::widget>
