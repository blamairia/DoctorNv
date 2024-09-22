<?php
namespace App\Filament\Resources;

use App\Filament\Resources\PatientResource\Pages;
use App\Models\Patient;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;

class PatientResource extends Resource
{
    protected static ?string $model = Patient::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('first_name')
                    ->label('First Name')
                    ->required()
                    ->maxLength(100),

                TextInput::make('last_name')
                    ->label('Last Name')
                    ->required()
                    ->maxLength(100),

                DatePicker::make('date_of_birth')
                    ->label('Date of Birth')
                    ->required(),

                Select::make('gender')
                    ->label('Gender')
                    ->options([
                        'Male' => 'Male',
                        'Female' => 'Female',
                    ])
                    ->required(),

                TextInput::make('address')
                    ->label('Address')
                    ->required()
                    ->maxLength(255),

                TextInput::make('phone_number')
                    ->label('Phone Number')
                    ->required()
                    ->maxLength(15),

                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->nullable(),

                Textarea::make('medical_history')
                    ->label('Medical History')
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('first_name')->label('First Name'),
                TextColumn::make('full_name')
                    ->label('Full Name')
                    ->getStateUsing(function (Patient $record) {
                        return $record->full_name;
                    }) // Use accessor to get the full name
                    ->sortable()
                    ->searchable(),
                TextColumn::make('last_name')->label('Last Name'),
                TextColumn::make('date_of_birth')->label('Date of Birth')->date(),
                TextColumn::make('gender')->label('Gender'),
                TextColumn::make('address')->label('Address'),
                TextColumn::make('phone_number')->label('Phone Number'),
            ])
            ->filters([
                // You can define filters here if needed
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

    public static function getRelations(): array
    {
        return [
            // Add related resources if any
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPatients::route('/'),
            'create' => Pages\CreatePatient::route('/create'),
            'edit' => Pages\EditPatient::route('/{record}/edit'),
        ];
    }
}
