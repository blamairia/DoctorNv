<?php

namespace App\Filament\Resources\PatientResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class VisitsRelationManager extends RelationManager
{
    protected static string $relationship = 'visits';

    // Remove static from this method
    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('visit_date')
                    ->label('Visit Date')
                    ->dateTime()
                    ->url(fn ($record) => url("/admin/visits/{$record->id}/edit"))
                    ->openUrlInNewTab(false),
                TextColumn::make('notes')
                    ->label('Notes'),
                TextColumn::make('diagnosis')
                    ->label('Diagnosis'),
            ])
            ->filters([
                // You can add filters if necessary
            ])
            ->headerActions([
                // Add any table header actions here
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
