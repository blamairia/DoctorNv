<?php

namespace App\Filament\Widgets;

use App\Models\Visit;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class VisitsOverview extends BaseWidget
{
    protected function getCards(): array
    {
        $today = Carbon::today();

        // Number of visits today
        $todaysVisits = Visit::whereDate('visit_date', $today)->count();

        // Total number of visits
        $allTimeVisits = Visit::count();

        return [
            Card::make('Visites Aujourd\'hui', $todaysVisits)
                ->description('Nombre de visites aujourd\'hui')
                ->color('primary'),

            Card::make('Visites Totales', $allTimeVisits)
                ->description('Nombre total de visites')
                ->color('primary'),
        ];
    }
}
