<?php
namespace App\Filament\Resources\PatientResource\RelationManagers;

use App\Filament\Resources\VisitResource;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class VisitsRelationManager extends RelationManager
{
    protected static string $relationship = 'visits'; // The relationship defined in your Patient model

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                // Make the visit date clickable
                TextColumn::make('visit_date')
                    ->label('Visit Date')
                    ->sortable()
                    ->url(fn ($record) => VisitResource::getUrl('edit', ['record' => $record->id]))  // Generate URL for the visit
                    ->openUrlInNewTab(),  // Optionally open in a new tab

                TextColumn::make('notes')->label('Notes'),
            ]);
              // Optional: Limit the number of records displayed per page
    }

    public static function getEagerRelations(): array
    {
        return ['patient'];  // Eager load the patient relationship
    }
}
