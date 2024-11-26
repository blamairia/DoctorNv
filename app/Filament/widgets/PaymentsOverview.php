<?php

namespace App\Filament\Widgets;

use App\Models\Visit;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class PaymentsOverview extends BaseWidget
{
    protected function getCards(): array
    {
        $today = Carbon::today();

        // Total payments and debts for today's visits
        $todaysPayments = Visit::whereDate('visit_date', $today)->sum('payment_total');
        $todaysDebts = Visit::whereDate('visit_date', $today)->sum('debt');

        // Total payments and debts for all-time visits
        $allTimePayments = Visit::sum('payment_total');
        $allTimeDebts = Visit::sum('debt');

        return [
            Card::make('Paiements Aujourd\'hui',  number_format($todaysPayments, 2) . ' da')
                ->description('Total des paiements pour les visites d\'aujourd\'hui')
                ->color('success'),

            Card::make('Dettes Aujourd\'hui',  number_format($todaysDebts, 2) . ' da')
                ->description('Total des dettes pour les visites d\'aujourd\'hui')
                ->color('danger'),

            Card::make('Paiements Totaux',  number_format($allTimePayments + $allTimeDebts, 2) . ' da')
                ->description('Total des paiements pour toutes les visites')
                ->color('success'),

            Card::make('Dettes Totales',  number_format($allTimeDebts, 2) . ' da')
                ->description('Total des dettes pour toutes les visites')
                ->color('danger'),
        ];
    }
}
