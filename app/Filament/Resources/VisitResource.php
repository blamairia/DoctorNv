<?php
namespace App\Filament\Resources;

use App\Filament\Resources\VisitResource\Pages;
use App\Models\Visit;
use App\Models\Patient;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;

class VisitResource extends Resource
{
    protected static ?string $model = Visit::class;

    public static function query(): Builder
    {
        return Visit::query();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(self::query()) // Define the query method to retrieve data
            ->columns([
                TextColumn::make('full_name')
                ->label('Patient Name')
                ->getStateUsing(fn ($record) => "{$record->patient->first_name} {$record->patient->last_name}")
                ->searchable(query: function (Builder $query, string $search): Builder {
                    return $query->whereHas('patient', function (Builder $subQuery) use ($search) {
                        $subQuery->where('first_name', 'like', "%{$search}%")
                                 ->orWhere('last_name', 'like', "%{$search}%");
                    });
                })
                ->sortable(),


                TextColumn::make('visit_date')
                    ->label('Visit Date')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                DateRangeFilter::make('visit_date')
                    ->label('Visit Date Range')
                    ->ranges([
                        'Today' => [now()->startOfDay(), now()->endOfDay()],
                        'Last 7 Days' => [now()->subDays(6)->startOfDay(), now()->endOfDay()],
                        'This Month' => [now()->startOfMonth(), now()->endOfMonth()],
                        'Last Month' => [
                            now()->subMonthNoOverflow()->startOfMonth(),
                            now()->subMonthNoOverflow()->endOfMonth(),
                        ],
                    ]),
            ])
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
            'index' => Pages\ListVisits::route('/'),
            'create' => Pages\CreateVisit::route('/create'),
            'edit' => Pages\EditVisit::route('/{record}/edit'),
        ];
    }
}
