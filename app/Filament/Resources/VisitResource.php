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
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Request;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;

class VisitResource extends Resource
{
    protected static ?string $model = Visit::class;

    public static function form(Form $form): Form
    {
        $patientId = Request::input('patient_id');
        return $form
            ->schema([
                Select::make('patient_id')
                    ->label('Patient')
                    ->searchable()
                    ->options(function () {
                        return Patient::all()->mapWithKeys(function ($patient) {
                            return [
                                $patient->id => "{$patient->first_name} {$patient->last_name}",
                            ];
                        });
                    })
                    ->default($patientId)
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function (callable $set) {
                        $set('appointment_id', null); // Réinitialiser appointment_id lorsque le patient change
                    }),

                Select::make('appointment_id')
                    ->label('Rendez-vous')
                    ->options(function (callable $get) {
                        $patientId = $get('patient_id');
                        if ($patientId) {
                            return Appointment::where('patient_id', $patientId)
                                ->get()
                                ->mapWithKeys(function ($appointment) {
                                    return [
                                        $appointment->id => "Rendez-vous le {$appointment->appointment_date} - {$appointment->reason}",
                                    ];
                                });
                        }
                        return [];
                    })
                    ->nullable(),

                DatePicker::make('visit_date')
                    ->label('Date de Visite')
                    ->default(now())
                    ->required(),

                Textarea::make('notes')
                    ->label('Notes'),

                Textarea::make('diagnosis')
                    ->label('Diagnostic'),

                DatePicker::make('follow_up_date')
                    ->label('Date de Suivi'),

                Textarea::make('blood_work_diagnostics')
                    ->label('Résultats de l\'Analyse de Sang')
                    ->placeholder('Saisissez les détails de l\'analyse de sang...'),

                Textarea::make('mri_scans')
                    ->label('IRM')
                    ->placeholder('Saisissez les détails des scans IRM...'),

                TextInput::make('payment_total')
                    ->label('Total Payé')
                    ->default(0)
                    ->required(),

                TextInput::make('debt')
                    ->label('Dette')
                    ->default(0)
                    ->required(),

                TextInput::make('payment_status')
                    ->label('Statut du Paiement')
                    ->default(fn ($record) => ($record->debt ?? 0) > 0 ? 'Non Payé' : 'Payé')
                    ->disabled()
                    ->hidden(),

                Textarea::make('xray_scans')
                    ->label('Radiographies')
                    ->placeholder('Saisissez les détails des radiographies...'),

                Repeater::make('prescriptions')
                    ->relationship('prescriptions')
                    ->columnSpan('full')
                    ->schema([
                        Grid::make(12)
                            ->schema([
                                Select::make('medicament_num_enr')
                                    ->label('Médicament')
                                    ->options(function () {
                                        return \App\Models\Medicament::all()->mapWithKeys(function ($medicament) {
                                            return [
                                                $medicament->num_enr => "{$medicament->nom_dci}, {$medicament->nom_com}, {$medicament->dosage} {$medicament->unite}, {$medicament->conditionnement}",
                                            ];
                                        });
                                    })
                                    ->searchable()
                                    ->required()
                                    ->columnSpan(7),

                                TextInput::make('dosage_instructions')
                                    ->label('Instructions de Dosage')
                                    ->required()
                                    ->columnSpan(3),

                                TextInput::make('quantity')
                                    ->label('Quantité')
                                    ->numeric()
                                    ->required()
                                    ->columnSpan(2),
                            ]),
                    ]),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')
                    ->label('Nom du Patient')
                    ->getStateUsing(fn ($record) => "{$record->patient->first_name} {$record->patient->last_name}")
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('patient', function (Builder $subQuery) use ($search) {
                            $subQuery->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%");
                        });
                    })
                    ->sortable(),

                TextColumn::make('visit_date')
                    ->label('Date de Visite')
                    ->date()
                    ->sortable(),

                TextColumn::make('diagnosis')
                    ->label('Diagnostic')
                    ->sortable()
                    ->searchable()
                    ->wrap()
                    ->limit(50)
                    ->tooltip(function ($record) {
                        return $record->diagnosis;
                    }),

                BooleanColumn::make('payment_status')
                    ->label('Statut du Paiement')
                    ->getStateUsing(function ($record) {
                        return $record->debt > 0;
                    })
                    ->trueIcon('heroicon-o-x-circle')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('danger')
                    ->falseColor('success'),

                BooleanColumn::make('has_prescriptions')
                    ->label('A des Prescriptions')
                    ->getStateUsing(function ($record) {
                        return $record->prescriptions()->exists();
                    })
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                TextColumn::make('payment_total')
                    ->label('Total Payé')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => number_format($state ?? 0, 2)),

                TextColumn::make('debt')
                    ->label('Dette')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => number_format($state ?? 0, 2)),
            ])
            ->filters([
                DateRangeFilter::make('visit_date')
                    ->label('Période de Visite')
                    ->ranges([
                        'Aujourd\'hui' => [now()->toDateString(), now()->toDateString()],
                        '7 Derniers Jours' => [now()->subDays(6)->startOfDay(), now()->endOfDay()],
                        'Ce Mois' => [now()->startOfMonth(), now()->endOfMonth()],
                        'Mois Dernier' => [
                            now()->subMonthNoOverflow()->startOfMonth(),
                            now()->subMonthNoOverflow()->endOfMonth(),
                        ],
                    ])
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
                    ->withIndicator()
                    ->disableCustomRange()
                    ->useRangeLabels(),
            ])
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
