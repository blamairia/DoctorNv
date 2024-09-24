<?php
namespace App\Filament\Resources;

use App\Filament\Resources\AppointmentResource\Pages;
use App\Models\Appointment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;

class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;



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

                DateTimePicker::make('appointment_date')
                    ->label('Appointment Date')
                    ->required(),

                Textarea::make('reason')
                    ->label('Reason')
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('patient.first_name')->label('First Name'),
                TextColumn::make('patient.last_name')->label('Last Name'),
                TextColumn::make('appointment_date')->label('Date')->dateTime(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAppointments::route('/'),
            'create' => Pages\CreateAppointment::route('/create'),
            'edit' => Pages\EditAppointment::route('/{record}/edit'),
        ];
    }
}
