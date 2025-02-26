<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Toggle;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class TransactionAnalyticsWidget extends Widget
{
    protected static string $view = 'filament.widgets.transaction-analytics-widget';

    // Estado del filtro
    protected int $daysAgo = 30;
    protected ?string $startDate = null;
    protected ?string $endDate = null;
    protected bool $showRegression = false;

    // Datos para la gráfica
    public array $data = [];
    public array $regressionData = [];
    
    public function mount()
    {
        $this->startDate = now()->subDays($this->daysAgo)->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
        $this->updateChartData();
    }

    protected function getFormSchema(): array
    {
        return [
            Grid::make(3)
                ->schema([
                    DatePicker::make('startDate')
                        ->label('Fecha Inicial')
                        ->default(now()->subDays($this->daysAgo))
                        ->reactive()
                        ->afterStateUpdated(fn () => $this->updateChartData()),
                    
                    DatePicker::make('endDate')
                        ->label('Fecha Final')
                        ->default(now())
                        ->reactive()
                        ->afterStateUpdated(fn () => $this->updateChartData()),
                    
                    Toggle::make('showRegression')
                        ->label('Mostrar Regresión Lineal')
                        ->default(false)
                        ->reactive()
                        ->afterStateUpdated(fn () => $this->updateChartData()),
                ]),
        ];
    }

    public function updateChartData()
    {
        // Validar fechas
        $startDate = $this->startDate ? Carbon::parse($this->startDate) : now()->subDays(30);
        $endDate = $this->endDate ? Carbon::parse($this->endDate) : now();
        
        // Obtener datos de transacciones agrupados por día
        $data = Trend::query(Transaction::query()
                ->where('created_at', '>=', $startDate)
                ->where('created_at', '<=', $endDate->endOfDay()))
            ->between(
                start: $startDate,
                end: $endDate,
            )
            ->perDay()
            ->sum('amount');

        $this->data = $data->map(fn (TrendValue $value) => [
            'date' => $value->date,
            'amount' => $value->aggregate,
        ])->toArray();

        // Calcular regresión lineal si está activada
        if ($this->showRegression) {
            $this->calculateLinearRegression();
        } else {
            $this->regressionData = [];
        }
    }

    protected function calculateLinearRegression()
    {
        // Solo calcular si hay suficientes datos
        if (count($this->data) < 2) {
            $this->regressionData = [];
            return;
        }

        // Preparar datos para regresión
        $x = [];
        $y = [];
        $timestamps = [];

        foreach ($this->data as $index => $point) {
            $x[] = $index;
            $y[] = $point['amount'];
            $timestamps[] = Carbon::parse($point['date'])->timestamp;
        }

        // Calcular regresión lineal
        $n = count($x);
        $sumX = array_sum($x);
        $sumY = array_sum($y);
        $sumXY = 0;
        $sumX2 = 0;

        for ($i = 0; $i < $n; $i++) {
            $sumXY += ($x[$i] * $y[$i]);
            $sumX2 += ($x[$i] * $x[$i]);
        }

        // Pendiente (m) e intercepción (b)
        $m = (($n * $sumXY) - ($sumX * $sumY)) / (($n * $sumX2) - ($sumX * $sumX));
        $b = ($sumY - ($m * $sumX)) / $n;

        // Generar puntos de regresión
        $this->regressionData = [];
        foreach ($x as $index => $value) {
            $predictedValue = ($m * $value) + $b;
            $this->regressionData[] = [
                'date' => $this->data[$index]['date'],
                'amount' => max(0, $predictedValue), // Evitar valores negativos
            ];
        }

        // Calcular predicción para los próximos 7 días si hay suficientes datos
        if (count($this->data) > 7) {
            $lastDate = Carbon::parse(end($this->data)['date']);
            
            for ($i = 1; $i <= 7; $i++) {
                $nextDate = $lastDate->copy()->addDays($i);
                $nextIndex = count($x) + $i - 1;
                $predictedValue = ($m * $nextIndex) + $b;
                
                $this->regressionData[] = [
                    'date' => $nextDate->format('Y-m-d'),
                    'amount' => max(0, $predictedValue),
                    'isPrediction' => true,
                ];
            }
        }
    }

    protected static function canView(): bool
    {
        // Puedes personalizar los permisos aquí
        return auth()->user()->can('view_analytics');
    }
}