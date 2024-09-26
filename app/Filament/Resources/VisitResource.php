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
                    $set('appointment_id', null); // Reset appointment_id when patient changes
                }),

                Select::make('appointment_id')
                ->label('Appointment')
                ->options(function (callable $get) {
                    $patientId = $get('patient_id');
                    if ($patientId) {
                        return Appointment::where('patient_id', $patientId)
                            ->get()
                            ->mapWithKeys(function ($appointment) {
                                return [
                                    $appointment->id => "Appointment on {$appointment->appointment_date} - {$appointment->reason}", // Customize as needed
                                ];
                            });
                    }
                    return []; // Return empty if no patient is selected
                })
                ->required(),



            DateTimePicker::make('visit_date')
                ->label('Visit Date')
                ->required(),

            Textarea::make('notes')
                ->label('Notes'),

            TextInput::make('diagnosis')
                ->label('Diagnosis'),

            DatePicker::make('follow_up_date')
                ->label('Follow-up Date'),
                 // New fields for diagnostics and imagery
            Textarea::make('blood_work_diagnostics')
            ->label('Blood Work Diagnostics')
            ->placeholder('Enter blood work details...'),

            Textarea::make('mri_scans')
                ->label('MRI Scans')
                ->placeholder('Enter MRI scan details...'),
                TextInput::make('payment_total')
                ->label('Payment Total')
                ->default(0)
                ->required(),

            TextInput::make('debt')
                ->label('Debt')
                ->default(0)
                ->required(),

            // Set the payment status field (optional, can be hidden)
            TextInput::make('payment_status')
                ->label('Payment Status')
                ->default(fn ($record) => ($record->debt ?? 0) > 0 ? 'Unpaid' : 'Paid')
                ->disabled()
                ->hidden(),
            Textarea::make('xray_scans')
                ->label('X-Ray Scans')
                ->placeholder('Enter X-Ray scan details...'),
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
                    ]),
        ]);

}


    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')
                ->label('Patient Name')
                ->getStateUsing(fn ($record) => "{$record->patient->first_name} {$record->patient->last_name}")
                ->searchable(query: function (Builder $query, string $search): Builder {
                    return $query->whereHas('patient', function (Builder $subQuery) use ($search) {
                        $subQuery->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%");
                    });
                })
                ->sortable(),

                Tables\Columns\TextColumn::make('visit_date')
                    ->label('Visit Date')
                    ->date()
                    ->sortable(),

                // Add the 'diagnosis' field with truncation and a wider column span
                Tables\Columns\TextColumn::make('diagnosis')
                    ->label('Diagnosis')
                    ->sortable()
                    ->searchable()
                    ->wrap() // Ensures text wraps if needed
                    ->limit(50) // Limits the displayed text to 50 characters
                    ->tooltip(function ($record) {
                        return $record->diagnosis; // Shows full text on hover
                    })
                    ->columnSpan(3), // Adjust this to make the column wider
                    BooleanColumn::make('payment_status')
                            ->label('Payment Status')
                            ->getStateUsing(function ($record) {
                                return $record->debt > 0; // True if unpaid, false if paid
                            })
                            ->trueIcon('heroicon-o-x-circle') // Unpaid icon
                            ->falseIcon('heroicon-o-check-circle') // Paid icon
                            ->trueColor('danger')
                            ->falseColor('success'),

                // Checkbox for prescriptions, making it smaller in width
                Tables\Columns\BooleanColumn::make('has_prescriptions')
                    ->label('Has Prescriptions')
                    ->getStateUsing(function ($record) {
                        return $record->prescriptions()->exists();
                    })
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->columnSpan(1),
                    TextColumn::make('payment_total')
                ->label('Total Paid')
                ->sortable()
                ->formatStateUsing(fn ($state) => number_format($state ?? 0, 2)),

            // Debt Column
            TextColumn::make('debt')
                ->label('Debt')
                ->sortable()
                ->formatStateUsing(fn ($state) => number_format($state ?? 0, 2)),
        // Smaller column span for the Boolean field
            ])
            ->filters([
                DateRangeFilter::make('visit_date')
                    ->label('Visit Date Range')
                    ->ranges([
                        'Today' => [now()->toDateString(), now()->toDateString()],
                        'Last 7 Days' => [now()->subDays(6)->startOfDay(), now()->endOfDay()],
                        'This Month' => [now()->startOfMonth(), now()->endOfMonth()],
                        'Last Month' => [
                            now()->subMonthNoOverflow()->startOfMonth(),
                            now()->subMonthNoOverflow()->endOfMonth(),
                        ],
                    ])

                    ->startDate(now()->startOfMonth())  // Default start date
                    ->endDate(now()->endOfMonth())      // Default end date
                    ->minDate(now()->subYear())         // Set minimum date to one year ago
                    ->maxDate(now())                    // Set maximum date to today
                    ->firstDayOfWeek(1)                 // Set Monday as the first day of the week
                    ->timePicker()                      // Enable time picker
                    ->timePicker24()                    // Use 24-hour format for time
                    ->timePickerIncrement(15)           // Increment time picker by 15 minutes
                    ->linkedCalendars()                 // Ensure linked calendars are enabled
                    ->alwaysShowCalendar()              // Always show the calendar view
                    ->separator(' to ')                 // Custom separator for date range
                    ->autoApply()                       // Auto apply changes without an "Apply" button
                    ->withIndicator()                   // Show filter active indicator
                    ->disableCustomRange()              // Disable custom date range selection
                    ->useRangeLabels(),                 // Use predefined range labels
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
