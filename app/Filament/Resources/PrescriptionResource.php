<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrescriptionResource\Pages;
use App\Filament\Resources\PrescriptionResource\RelationManagers;
use App\Models\Medicament;
use App\Models\Prescription;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PrescriptionResource extends Resource
{
    protected static ?string $model = Prescription::class;



    public static function form(Form $form): Form
{
    return $form
        ->schema([
            Select::make('medicament_num_enr')
            ->label('Medicament')
            ->relationship('medicament', 'nom_com')  // Use nom_com for label display
            ->getSearchResultsUsing(fn (string $query) =>
                Medicament::where('nom_com', 'like', "%{$query}%")
                    ->pluck('nom_com', 'num_enr')  // Ensure num_enr is cast as a string
            )
            ->getOptionLabelUsing(fn ($value) =>
                Medicament::where('num_enr', (string)$value)->first()->nom_com ?? null  // Casting num_enr as string
            )
            ->searchable()
            ->required(),

            Forms\Components\TextInput::make('dosage_instructions')
                ->label('Dosage Instructions')
                ->required(),
            Forms\Components\TextInput::make('quantity')
                ->label('Quantity')
                ->numeric()
                ->required(),
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
    public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('visit.visit_date')->label('Visit Date')->dateTime(),

            Tables\Columns\TextColumn::make('id')->label('ID'),
            Tables\Columns\TextColumn::make('dosage_instructions')->label('Dosage Instructions'),
        ])
        ->filters([])
        ->defaultSort('updated_at', 'desc')
        ->actions([
            Tables\Actions\EditAction::make(),
        ])
        ->bulkActions([
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
            ]),
        ]);
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
            'index' => Pages\ListPrescriptions::route('/'),
            'create' => Pages\CreatePrescription::route('/create'),
            'edit' => Pages\EditPrescription::route('/{record}/edit'),
        ];
    }
}
