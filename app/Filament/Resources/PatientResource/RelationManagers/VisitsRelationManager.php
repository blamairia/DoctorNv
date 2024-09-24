<?php
namespace App\Filament\Resources\PatientResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class VisitsRelationManager extends RelationManager
{
    protected static string $relationship = 'visits'; // The relationship defined in your Patient model

    public  function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('visit_date')->label('Visit Date')->sortable(),
                TextColumn::make('notes')->label('Notes'),
            ]);
            // Limit the number of records displayed per page
    }
    public static function getEagerRelations(): array
    {
        return ['patient'];  // Eager load the patient relationship
    }
}
