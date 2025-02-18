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
    protected static ?string $heading = 'Ingresos / transacciones fallidas';
 
    protected function getData(): array
    {
        // Obtener el inicio del año anterior
        $startDate = now()->subYear()->startOfYear();

        // Obtener el final del mes actual
        $endDate = now()->endOfMonth();

        // Obtener transacciones con status 'succeeded'
        $succeededTransactions = Transaction::where('status', 'succeeded')
            ->whereBetween('create_time', [$startDate, $endDate])
            ->selectRaw('DATE_FORMAT(create_time, "%Y-%m") as month, SUM(amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Obtener transacciones con status 'failed'
        $failedTransactions = Transaction::where('status', 'failed')
            ->whereBetween('create_time', [$startDate, $endDate])
            ->selectRaw('DATE_FORMAT(create_time, "%Y-%m") as month, SUM(amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Mapear los datos de transacciones exitosas
        $succeededData = $succeededTransactions->map(function ($transaction) {
            return [
                'date' => $transaction->month,
                'aggregate' => $transaction->total,
            ];
        });

        // Mapear los datos de transacciones fallidas
        $failedData = $failedTransactions->map(function ($transaction) {
            return [
                'date' => $transaction->month,
                'aggregate' => $transaction->total,
            ];
        });

        // Combinar las etiquetas (labels) de ambos conjuntos de datos
        $labels = $succeededData->pluck('date')->merge($failedData->pluck('date'))->unique()->sort()->values();

        return [
            'datasets' => [
                [
                    'label' => 'Ingresos',
                    'data' => $labels->map(function ($date) use ($succeededData) {
                        return $succeededData->where('date', $date)->first()['aggregate'] ?? 0;
                    }),
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)', // Color para succeeded
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                ],
                [
                    'label' => 'Transacciones fallidas',
                    'data' => $labels->map(function ($date) use ($failedData) {
                        return $failedData->where('date', $date)->first()['aggregate'] ?? 0;
                    }),
                    'backgroundColor' => 'rgba(255, 99, 132, 0.2)', // Color para failed
                    'borderColor' => 'rgba(255, 99, 132, 1)',
                ],
            ],
            'labels' => $labels->map(function ($date) {
                return Carbon::createFromFormat('Y-m', $date)->translatedFormat('M Y');
            }),
        ];
    }
 
    protected function getType(): string
    {
        return 'bar';
    }
}
