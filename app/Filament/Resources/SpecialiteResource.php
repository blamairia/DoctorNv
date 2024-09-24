<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SpecialiteResource\Pages;
use App\Filament\Resources\SpecialiteResource\RelationManagers;
use App\Models\Specialite;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SpecialiteResource extends Resource
{
    protected static ?string $model = Specialite::class;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    public static function shouldRegisterNavigation(): bool
    {
        return false; // Hide from sidebar
    }

    // This prevents users from directly accessing the Prescription list page
    public static function canViewAny(): bool
    {
        return false; // Users cannot view the list of prescriptions
    }
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSpecialites::route('/'),
            'create' => Pages\CreateSpecialite::route('/create'),
            'edit' => Pages\EditSpecialite::route('/{record}/edit'),
        ];
    }
}
