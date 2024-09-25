<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VisitResource\Pages;
use App\Models\Visit;
use App\Models\Patient;
use App\Models\Appointment;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;

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
                ->options(function () {
                    return \App\Models\Patient::all()->mapWithKeys(function ($patient) {
                        return [
                            $patient->id => "{$patient->first_name} {$patient->last_name}",
                        ];
                    });
                })
                ->required()
                ->reactive()  // Ensures the next field updates when this field changes
                ->afterStateUpdated(function (callable $set) {
                    $set('appointment_id', null); // Reset the appointment field when the patient changes
                }),

            Select::make('appointment_id')
                ->label('Appointment')
                ->options(function (callable $get) {
                    $patientId = $get('patient_id');
                    if ($patientId) {
                        // Fetch appointments for the selected patient only
                        return \App\Models\Appointment::where('patient_id', $patientId)
                            ->pluck('appointment_date', 'id');
                    }
                    return [];  // No appointments if no patient selected
                })
                ->disabled(fn (callable $get) => !$get('patient_id'))  // Disable until a patient is selected
                ->required(),


                DateTimePicker::make('visit_date')
                    ->label('Visit Date')
                    ->default(now())
                    ->required(),

                Textarea::make('notes')
                    ->label('Notes')
                    ->nullable(),

                Textarea::make('diagnosis')
                    ->label('Diagnosis')
                    ->nullable(),

                DatePicker::make('follow_up_date')
                    ->label('Follow-Up Date')
                    ->nullable(),
                     // Add the prescription repeater to the form
                     Repeater::make('prescriptions')
                     ->relationship('prescriptions')
                     ->columnSpan('full') // This ensures the Repeater takes the full width of the form
                     ->schema([
                         Grid::make(12) // Adjusting the internal grid to 12 columns to span the full width
                             ->schema([
                                 Select::make('medicament_num_enr')
                                     ->label('Medicament')
                                     ->options(function () {
                                         return \App\Models\Medicament::all()->mapWithKeys(function ($medicament) {
                                             return [
                                                 $medicament->num_enr => "{$medicament->nom_dci}, {$medicament->nom_com}, {$medicament->dosage} {$medicament->unite}, {$medicament->conditionnement}",
                                             ];
                                         });
                                     })
                                     ->searchable()
                                     ->required()
                                     ->columnSpan(7), // Takes up 8 columns out of 12 (about two-thirds of the width)

                                 TextInput::make('dosage_instructions')
                                     ->label('Dosage Instructions')
                                     ->required()
                                     ->columnSpan(3), // Takes up 2 columns

                                 TextInput::make('quantity')
                                     ->label('Quantity')
                                     ->numeric()
                                     ->required()
                                     ->columnSpan(2), // Takes up 2 columns
                             ]),
                     ])



                , // To display each prescription input in columns
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('patient.full_name')->label('Patient'),
                Tables\Columns\TextColumn::make('visit_date')->label('Visit Date')->date(),
            ])
            ->filters([])
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\DeleteBulkAction::make()]);
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
