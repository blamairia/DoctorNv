<?php



namespace App\Filament\Resources\PatientResource\RelationManagers;

use App\Filament\Resources\VisitResource;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\DateTimeColumn;
use Filament\Tables\Filters\SelectFilter;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use App\Models\Visit;

class VisitsRelationManager extends RelationManager
{
    protected static string $relationship = 'visits'; // The relationship defined in your Patient model

    public function table(Table $table): Table
    {
        return $table
            ->columns($this->getVisitColumns())
            ->filters($this->getVisitFilters())
            ->actions($this->getVisitActions())
            ->bulkActions($this->getVisitBulkActions())
            ->headerActions([ // Optional: Add any header actions if needed
                // Example: Action::make('Create Visit')->url(VisitResource::getUrl('create'))
            ]);
    }

    protected function getVisitColumns(): array
    {
        return [
            TextColumn::make('visit_date')
            ->label('Visit Date')
            ->sortable()
            ->url(fn ($record) => VisitResource::getUrl('edit', ['record' => $record->id]))  // Generate URL for the visit
            ->openUrlInNewTab(),
            TextColumn::make('notes')
                ->label('Notes')
                ->sortable(),

                Tables\Columns\TextColumn::make('diagnosis')
                    ->label('Diagnosis')
                    ->sortable()
                    ->searchable()
                    ->wrap() // Ensures text wraps if needed
                    ->limit(50) // Limits the displayed text to 50 characters
                    ->tooltip(function ($record) {
                        return $record->diagnosis; // Shows full text on hover
                    })
                    ->columnSpan(3),
                    Tables\Columns\TextColumn::make('reason')
                        ->label('Reason')
                        ->sortable()
                        ->searchable()
                        ->wrap() // Ensures text wraps if needed
                        ->limit(50) // Limits the displayed text to 50 characters
                        ->tooltip(function ($record) {
                            return $record->reason; // Shows full text on hover
                        })
                        ->columnSpan(3),

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


        ];
    }

    protected function getVisitFilters(): array
    {
        return [

            DateRangeFilter::make('visit_date')
                ->label('Visit Date Range'),
        ];
    }

    protected function getVisitActions(): array
    {
        return [
            // Add any actions specific to the visits table
        ];
    }

    protected function getVisitBulkActions(): array
    {
        return [
            // Add any bulk actions if needed
        ];
    }

    public static function getEagerRelations(): array
    {
        return ['patient'];  // Eager load the patient relationship
    }
}
