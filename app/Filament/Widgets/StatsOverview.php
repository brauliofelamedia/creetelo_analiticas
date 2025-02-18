<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $bestMonth = $this->getBestMonth();
        $worstMonth = $this->getWorstMonth();

        return [
            Stat::make('Ganancias año actual', $this->formatAmount($this->getCurrentYearEarnings()))
                ->description('Total año en curso')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('warning'),

            Stat::make('Ganancias mes anterior', $this->formatAmount($this->getLastMonthEarnings()))
                ->description('Total mes anterior')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('info'),

            Stat::make('Ganancias mes actual', $this->formatAmount($this->getCurrentMonthEarnings()))
                ->description('Total mes en curso')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Mejor mes: ' . $this->spanishMonths[$bestMonth['month']], $this->formatAmount($bestMonth['total']))
                ->description('Mes con mayores ingresos')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
                
            Stat::make('Mes más bajo: ' . $this->spanishMonths[$worstMonth['month']], $this->formatAmount($worstMonth['total']))
                ->description('Mes con menores ingresos')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),
            
            Stat::make('Ganancias totales', '$'.number_format($this->getTotalEarnings(),0).' USD')
                ->description('Ganancias acumuladas totales')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('primary'),
        ];
    }
    
    private function getTotalEarnings()
    {
        return Transaction::query()
            ->where('status', 'succeeded')
            ->sum('amount');
    }

    private function getCurrentMonthEarnings()
    {
        return Transaction::query()
            ->whereMonth('create_time', Carbon::now()->month)
            ->whereYear('create_time', Carbon::now()->year)
            ->where('status', 'succeeded')
            ->sum('amount');
    }

    private function getLastMonthEarnings()
    {
        return Transaction::query()
            ->whereMonth('create_time', Carbon::now()->subMonth()->month)
            ->whereYear('create_time', Carbon::now()->subMonth()->year)
            ->where('status', 'succeeded')
            ->sum('amount');
    }

    private function getCurrentYearEarnings()
    {
        return Transaction::query()
            ->whereYear('create_time', Carbon::now()->year)
            ->where('status', 'succeeded')
            ->sum('amount');
    }

    private function getWorstMonth()
    {
        $result = Transaction::query()
            ->select(
                DB::raw('MONTH(create_time) as month'),
                DB::raw('SUM(amount) as total')
            )
            ->whereYear('create_time', Carbon::now()->year)
            ->where('status', 'succeeded')
            ->groupBy('month')
            ->orderBy('total', 'asc')
            ->first();

        return [
            'month' => $result?->month ?? 1,
            'total' => $result?->total ?? 0
        ];
    }

    private function getBestMonth()
    {
        $result = Transaction::query()
            ->select(
                DB::raw('MONTH(create_time) as month'),
                DB::raw('SUM(amount) as total')
            )
            ->whereYear('create_time', Carbon::now()->year)
            ->where('status', 'succeeded')
            ->groupBy('month')
            ->orderBy('total', 'desc')
            ->first();

        return [
            'month' => $result?->month ?? 1,
            'total' => $result?->total ?? 0
        ];
    }

    private function formatAmount($amount)
    {
        return '$' . number_format($amount, 2).' USD';
    }

    protected $spanishMonths = [
        1 => 'Enero',
        2 => 'Febrero',
        3 => 'Marzo',
        4 => 'Abril',
        5 => 'Mayo',
        6 => 'Junio',
        7 => 'Julio',
        8 => 'Agosto',
        9 => 'Septiembre',
        10 => 'Octubre',
        11 => 'Noviembre',
        12 => 'Diciembre'
    ];
}
