<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class MonthlyEarningsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $bestMonth = $this->getBestMonth();
        $worstMonth = $this->getWorstMonth();

        return [
            Stat::make('Mejor mes ' . $bestMonth['month_name'], $this->formatAmount($bestMonth['total']))
                ->description('Mes con mayores ingresos')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
                
            Stat::make('Mes más bajo ' . $worstMonth['month_name'], $this->formatAmount($worstMonth['total']))
                ->description('Mes con menores ingresos')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),
        ];
    }

    private function getBestMonth()
    {
        $result = Transaction::query()
            ->select(
                DB::raw('MONTH(create_time) as month'),
                DB::raw('MONTHNAME(create_time) as month_name'),
                DB::raw('SUM(amount) as total')
            )
            ->whereYear('create_time', Carbon::now()->year)
            ->where('status', 'succeeded')
            ->groupBy('month', 'month_name')
            ->orderBy('total', 'desc')
            ->first();

        return [
            'month_name' => $result?->month_name ?? 'N/A',
            'total' => $result?->total ?? 0
        ];
    }

    private function getWorstMonth()
    {
        $result = Transaction::query()
            ->select(
                DB::raw('MONTH(create_time) as month'),
                DB::raw('MONTHNAME(create_time) as month_name'),
                DB::raw('SUM(amount) as total')
            )
            ->whereYear('create_time', Carbon::now()->year)
            ->where('status', 'succeeded')
            ->groupBy('month', 'month_name')
            ->orderBy('total', 'asc')
            ->first();

        return [
            'month_name' => $result?->month_name ?? 'N/A',
            'total' => $result?->total ?? 0
        ];
    }

    private function formatAmount($amount)
    {
        return '$' . number_format($amount, 2);
    }
}
