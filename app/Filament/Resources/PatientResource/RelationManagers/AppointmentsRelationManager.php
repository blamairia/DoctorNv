<?php
namespace App\Filament\Resources\PatientResource\RelationManagers;

use App\Filament\Resources\AppointmentResource;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class AppointmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'appointments';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('appointment_date')->label('Appointment Date')
                ->sortable()
                ->url(fn ($record) => AppointmentResource::getUrl('edit', ['record' => $record->id]))  // Generate URL for the visit
                ->openUrlInNewTab()->dateTime(),
                TextColumn::make('reason')->label('Reason'),
            ]) ;

    }
    public static function getEagerRelations(): array
    {
        return ['patient'];  // Eager load the patient relationship
    }
}
