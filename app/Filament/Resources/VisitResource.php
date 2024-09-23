<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VisitResource\Pages;
use App\Models\Visit;
use App\Models\Medicament;
use App\Models\Patient;
use App\Models\Appointment;
use Filament\Resources\Resource;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;

class VisitResource extends Resource
{
    protected static ?string $model = Visit::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('patient_id')
                    ->label('Patient')
                    ->searchable()
                    ->options(function (string $search = null) {
                        return Patient::query()
                            ->when($search, function (Builder $query) use ($search) {
                                $query->whereRaw("LOWER(CONCAT(first_name, ' ', last_name)) LIKE ?", ['%' . strtolower($search) . '%']);
                            })
                            ->get()
                            ->mapWithKeys(function ($patient) {
                                return [$patient->id => $patient->first_name . ' ' . $patient->last_name];
                            });
                    })
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function (callable $set) {
                        $set('appointment_id', null); // Reset appointment when patient changes
                    }),

                Select::make('appointment_id')
                    ->label('Appointment')
                    ->options(function (callable $get) {
                        $patientId = $get('patient_id');
                        if ($patientId) {
                            return Appointment::where('patient_id', $patientId)
                                ->pluck('appointment_date', 'id');
                        }
                        return [];
                    })
                    ->disabled(fn (callable $get) => !$get('patient_id'))
                    ->required(),

                DateTimePicker::make('visit_date')
                    ->label('Visit Date')
                    ->default(now()) // Prefill with current date and time
                    ->required(),

                Textarea::make('notes')
                    ->label('Notes')
                    ->nullable(),

                Textarea::make('diagnosis')
                    ->label('Diagnosis')
                    ->nullable(),

                DatePicker::make('follow_up_date')
                    ->label('Follow-Up Date')
                    ->nullable()
                    ->afterStateUpdated(function (callable $get, callable $set) {
                        // Trigger the add appointment modal when follow-up date is selected
                        if ($get('follow_up_date')) {
                            $set('appointment_modal', true);
                        }
                    }),

                Repeater::make('prescriptions')
                    ->relationship('prescriptions')
                    ->schema([
                        Select::make('medicament_num_enr')
                            ->label('Medicament')
                            ->relationship('medicament', 'nom_com')
                            ->searchable()
                            ->required(),
                        TextInput::make('dosage_instructions')
                            ->label('Dosage Instructions')
                            ->required(),
                        TextInput::make('quantity')
                            ->label('Quantity')
                            ->numeric()
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('patient.full_name')->label('Patient'),
                TextColumn::make('visit_date')->label('Visit Date')->dateTime(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVisits::route('/'),
            'create' => Pages\CreateVisit::route('/create'),
            'edit' => Pages\EditVisit::route('/{record}/edit'),
        ];
    }
}
