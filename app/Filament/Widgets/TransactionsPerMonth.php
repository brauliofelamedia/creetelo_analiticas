<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class TransactionsPerMonth extends ChartWidget
{
    protected static ?string $heading = 'Ingresos x mes';
 
    protected function getData(): array
    {
        // Obtener el inicio del año anterior
        $startDate = now()->subYear()->startOfYear();

        // Obtener el final del mes actual
        $endDate = now()->endOfMonth();

        $transactions = Transaction::where('status', 'succeeded')
            ->whereBetween('create_time', [$startDate, $endDate])
            ->selectRaw('DATE_FORMAT(create_time, "%Y-%m") as month, SUM(amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $data = $transactions->map(function ($transaction) {
            return [
                'date' => $transaction->month,
                'aggregate' => $transaction->total,
            ];
        });

        return [
            'datasets' => [
                [
                    'label' => 'Ingresos Mensuales',
                    'data' => $data->pluck('aggregate'),
                ],
            ],
            'labels' => $data->pluck('date')->map(function ($date) {
                return Carbon::createFromFormat('Y-m', $date)->translatedFormat('M Y');
            }),
        ];
    }
 
    protected function getType(): string
    {
        return 'line';
    }
}
