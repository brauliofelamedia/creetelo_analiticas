<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class TransactionsPerDay extends ChartWidget
{
    protected static ?string $heading = 'Ingresos x día / mes actual';
 
    protected function getData(): array
    {
        // Obtener el inicio del mes actual
        $startDate = now()->startOfMonth();

        // Obtener el final del mes actual
        $endDate = now()->endOfMonth();

        // Obtener transacciones con status 'succeeded' del mes actual
        $succeededTransactions = Transaction::where('status', 'succeeded')
            ->whereBetween('create_time', [$startDate, $endDate])
            ->selectRaw('DATE_FORMAT(create_time, "%Y-%m-%d") as day, SUM(amount) as total')
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        // Mapear los datos de transacciones exitosas
        $succeededData = $succeededTransactions->map(function ($transaction) {
            return [
                'day' => $transaction->day,
                'aggregate' => $transaction->total,
            ];
        });

        // Crear un array con todos los días del mes
        $daysInMonth = [];
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $daysInMonth[] = $currentDate->format('Y-m-d');
            $currentDate->addDay();
        }
        
        return [
            'datasets' => [
                [
                    'label' => 'Ingresos',
                    'data' => collect($daysInMonth)->map(function ($day) use ($succeededData) {
                        return $succeededData->where('day', $day)->first()['aggregate'] ?? 0;
                    }),
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)', // Color para succeeded
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                ],
            ],
            'labels' => collect($daysInMonth)->map(function ($day) {
                return Carbon::createFromFormat('Y-m-d', $day)->translatedFormat('d M Y');
            }),
        ];
    }
 
    protected function getType(): string
    {
        return 'bar';
    }
}
