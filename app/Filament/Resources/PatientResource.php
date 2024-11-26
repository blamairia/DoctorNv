<?php


namespace App\Filament\Resources;

use App\Filament\Resources\PatientResource\Pages;
use App\Filament\Resources\PatientResource\RelationManagers\AppointmentsRelationManager;
use App\Filament\Resources\PatientResource\RelationManagers\VisitsRelationManager;
use App\Models\Patient;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use Carbon\Carbon;

class PatientResource extends Resource
{
    protected static ?string $model = Patient::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('first_name')
                    ->label('Prénom')
                    ->required()
                    ->maxLength(100),

                TextInput::make('last_name')
                    ->label('Nom de Famille')
                    ->required()
                    ->maxLength(100),

                DatePicker::make('date_of_birth')
                    ->label('Date de Naissance')
                    ->required(),

                Select::make('gender')
                    ->label('Genre')
                    ->options([
                        'Male' => 'Homme',
                        'Female' => 'Femme',
                    ])
                    ->required(),

                TextInput::make('address')
                    ->label('Adresse')
                    ->required()
                    ->maxLength(255),

                TextInput::make('phone_number')
                    ->label('Numéro de Téléphone')
                    ->required()
                    ->maxLength(15),

                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->nullable(),

                Textarea::make('medical_history')
                    ->label('Historique Médical')
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')
                    ->label('Nom Complet')
                    ->getStateUsing(fn (Patient $record) => "{$record->first_name} {$record->last_name}")
                    ->sortable()
                    ->searchable(query: function ($query, string $search) {
                        return $query->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    }),

                

                    TextColumn::make('last_visit')
                    ->label('Dernière Visite')
                    ->getStateUsing(fn (Patient $record) => 
                        $record->visits()->latest('visit_date')->value('visit_date')
                            ? Carbon::parse($record->visits()->latest('visit_date')->value('visit_date'))->format('d/m/Y')
                            : '❌ Pas de visites'
                    )
                    ->sortable(),
                    

                TextColumn::make('created_at')
                    ->label('Date de D/ajoute')
                    ->sortable()
                    ->date(),
                

                TextColumn::make('gender')
                    ->label('Genre'),

                TextColumn::make('address')
                    ->label('Adresse'),
                TextColumn::make('date_of_birth')
                    ->label('Date de Naissance')
                    ->sortable()
                    ->date(),
                

                TextColumn::make('phone_number')
                    ->label('Numéro de Téléphone'),
            ])
            ->filters([
                DateRangeFilter::make('created_at')
                    ->label('Période de Création')
                    ->startDate(now()->startOfMonth())
                    ->endDate(now()->endOfMonth())
                    ->minDate(now()->subYear())
                    ->maxDate(now())
                    ->firstDayOfWeek(1)
                    ->timePicker()
                    ->timePicker24()
                    ->timePickerIncrement(15)
                    ->linkedCalendars()
                    ->alwaysShowCalendar()
                    ->separator(' à ')
                    ->autoApply()
                    ->useRangeLabels(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            VisitsRelationManager::class,
            AppointmentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPatients::route('/'),
            'create' => Pages\CreatePatient::route('/create'),
            'edit' => Pages\EditPatient::route('/{record}/edit'),
        ];
    }
}
