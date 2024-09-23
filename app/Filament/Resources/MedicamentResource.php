<?php
namespace App\Filament\Resources;
use Filament\Tables\Columns\BooleanColumn;


use App\Filament\Resources\MedicamentResource\Pages;
use App\Models\Medicament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class MedicamentResource extends Resource
{
    protected static ?string $model = Medicament::class;

    public static function getNavigationIcon(): string
    {
        return 'tabler-pill'; // Example icon for a medicament (you can choose a pill or related icon)
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nom_com')
                    ->label('Commercial Name')
                    ->required(),
                Forms\Components\TextInput::make('nom_dci')
                    ->label('DCI Name')
                    ->required(),
                Forms\Components\TextInput::make('dosage')
                    ->label('Dosage')
                    ->required(),
                Forms\Components\TextInput::make('unite')
                    ->label('Unit')
                    ->required(),
                Forms\Components\TextInput::make('conditionnement')
                    ->label('Conditioning')
                    ->required(),
                Forms\Components\Checkbox::make('remboursable')
                    ->label('Refundable'),
                Forms\Components\DatePicker::make('date_remboursement')
                    ->label('Refund Date')
                    ->nullable(),
                Forms\Components\TextInput::make('tarif_ref')
                    ->label('Reference Price')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nom_com')->label('Commercial Name'),
                TextColumn::make('nom_dci')->label('DCI Name'),
                TextColumn::make('dosage')->label('Dosage'),
                TextColumn::make('unite')->label('Unit'),
                TextColumn::make('conditionnement')->label('Packaging'),
                BooleanColumn::make('remboursable')->label('Reimbursable'),
                BooleanColumn::make('hopital')->label('For Hospital Use'),
                BooleanColumn::make('secteur_sanitaire')->label('Public Health Sector'),
                BooleanColumn::make('officine')->label('For Pharmacies'),
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
