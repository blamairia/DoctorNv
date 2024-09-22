<?php
namespace App\Filament\Resources;

use App\Filament\Resources\VisitResource\Pages;
use App\Models\Visit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Columns\TextColumn;

class VisitResource extends Resource
{
    protected static ?string $model = Visit::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('patient_id')
                    ->label('Patient')
                    ->options(function () {
                        return \App\Models\Patient::all()->mapWithKeys(function ($patient) {
                            return [$patient->id => $patient->first_name . ' ' . $patient->last_name];
                        });
                    })
                    ->required(),

                Select::make('appointment_id')
                    ->label('Appointment')
                    ->relationship('appointment', 'appointment_date')
                    ->nullable(),

                DateTimePicker::make('visit_date')
                    ->label('Visit Date')
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

                Repeater::make('prescriptions')
                    ->relationship('prescriptions')
                    ->schema([
                        Select::make('medicament_id')
                            ->label('Medicament')
                            ->relationship('medicament', 'nom_com')
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
                TextColumn::make('patient.first_name')->label('First Name'),
                TextColumn::make('patient.last_name')->label('Last Name'),
                TextColumn::make('visit_date')->label('Visit Date')->dateTime(),
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
