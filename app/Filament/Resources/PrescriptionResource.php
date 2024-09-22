<?php
namespace App\Filament\Resources;

use App\Filament\Resources\PrescriptionResource\Pages;
use App\Models\Prescription;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class PrescriptionResource extends Resource
{
    protected static ?string $model = Prescription::class;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('visit_id')
                    ->label('Visit')
                    ->relationship('visit', 'visit_date')
                    ->required(),

                Select::make('medicament_id')
                    ->label('Medicament')
                    ->relationship('medicament', 'nom_com')
                    ->required(),

                TextInput::make('dosage_instructions')
                    ->label('Dosage Instructions')
                    ->required(),

                TextInput::make('quantity')
                    ->label('Quantity')
                    ->numeric()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('visit.visit_date')->label('Visit Date')->dateTime(),
                Tables\Columns\TextColumn::make('medicament.nom_com')->label('Medicament'),
                Tables\Columns\TextColumn::make('dosage_instructions')->label('Dosage Instructions'),
            ]);
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
