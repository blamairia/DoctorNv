<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MedicamentResource\Pages;
use App\Models\Medicament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BooleanColumn;

class MedicamentResource extends Resource
{
    protected static ?string $model = Medicament::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nom_com')
                    ->label('Nom Commercial')
                    ->required(),

                Forms\Components\TextInput::make('nom_dci')
                    ->label('Nom DCI')
                    ->required(),

                Forms\Components\TextInput::make('dosage')
                    ->label('Dosage')
                    ->required(),

                Forms\Components\TextInput::make('unite')
                    ->label('Unité')
                    ->required(),

                Forms\Components\TextInput::make('conditionnement')
                    ->label('Conditionnement')
                    ->required(),

                Forms\Components\Checkbox::make('remboursable')
                    ->label('Remboursable'),

                Forms\Components\DatePicker::make('date_remboursement')
                    ->label('Date de Remboursement')
                    ->nullable(),

                Forms\Components\TextInput::make('tarif_ref')
                    ->label('Tarif de Référence')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nom_com')
                    ->label('Nom Commercial')
                    ->searchable(),

                TextColumn::make('nom_dci')
                    ->label('Nom DCI')
                    ->searchable(),

                TextColumn::make('dosage')
                    ->label('Dosage'),

                TextColumn::make('unite')
                    ->label('Unité'),

                TextColumn::make('conditionnement')
                    ->label('Conditionnement'),

                BooleanColumn::make('remboursable')
                    ->label('Remboursable'),

                BooleanColumn::make('hopital')
                    ->label('Utilisation Hospitalière'),

                BooleanColumn::make('secteur_sanitaire')
                    ->label('Secteur Sanitaire Public'),

                BooleanColumn::make('officine')
                    ->label('Utilisation en Pharmacie'),
            ])
            ->filters([]) // Add filters if necessary
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMedicaments::route('/'),
            'create' => Pages\CreateMedicament::route('/create'),
            'edit' => Pages\EditMedicament::route('/{record}/edit'),
        ];
    }
}
