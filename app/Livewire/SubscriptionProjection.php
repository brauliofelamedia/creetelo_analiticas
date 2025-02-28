<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Subscription;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SubscriptionProjection extends Component implements HasForms
{
    use InteractsWithForms;

    public $projectionPeriod = 'trimestral';
    public $subscriptionData = [];
    public $projectedData = [];

    public function mount()
    {
        $this->calculateSubscriptions();
    }

    protected function getFormSchema(): array
    {
        return [
            Select::make('projectionPeriod')
                ->label('Período de Proyección')
                ->options([
                    'trimestral' => 'Trimestral (3 meses)',
                    'semestral' => 'Semestral (6 meses)',
                    'anual' => 'Anual (12 meses)',
                ])
                ->default('trimestral')
                ->reactive()
                ->afterStateUpdated(fn () => $this->calculateSubscriptions()),
        ];
    }

    private function calculateProjections($historicalData)
    {
        if (empty($historicalData)) return [];

        // Obtener los últimos 6 meses para calcular la tendencia
        $recentData = array_slice($historicalData, -6);
        
        // Calcular el promedio de crecimiento mensual
        $growthRates = [];
        $totals = array_column($recentData, 'total');
        
        for ($i = 1; $i < count($totals); $i++) {
            if ($totals[$i-1] > 0) {
                $growthRates[] = ($totals[$i] - $totals[$i-1]) / $totals[$i-1];
            }
        }
        
        // Si no hay datos suficientes, usar un crecimiento base del 5%
        $avgGrowthRate = !empty($growthRates) 
            ? max(array_sum($growthRates) / count($growthRates), 0.05)
            : 0.05;

        $monthsToProject = match($this->projectionPeriod) {
            'trimestral' => 3,
            'semestral' => 6,
            'anual' => 12,
            default => 3,
        };

        $projections = [];
        $lastData = end($historicalData);
        $lastMonth = Carbon::parse($lastData['month']);
        
        $previousData = $lastData;
        
        // Valores base para proyección
        $lastTotal = $lastData['total'];
        $lastActive = $lastData['active'];
        $lastCanceled = $lastData['canceled'];
        $lastPastDue = $lastData['past_due'];
        $lastIncomplete = $lastData['incomplete_expired'];
        $lastAmount = $lastData['total_amount'];

        for ($i = 1; $i <= $monthsToProject; $i++) {
            $growthFactor = pow(1 + $avgGrowthRate, $i);
            
            $currentProjection = [
                'month' => $lastMonth->copy()->addMonth($i)->format('Y-m'),
                'total' => round($lastTotal * $growthFactor),
                'active' => round($lastActive * $growthFactor),
                'canceled' => round($lastCanceled * $growthFactor),
                'past_due' => round($lastPastDue * $growthFactor),
                'incomplete_expired' => round($lastIncomplete * $growthFactor),
                'total_amount' => round($lastAmount * $growthFactor, 2),
                'is_projection' => true,
                'active_growth' => $this->calculateGrowthPercentage(
                    round($lastActive * $growthFactor),
                    $previousData['active']
                ),
                'canceled_growth' => $this->calculateGrowthPercentage(
                    round($lastCanceled * $growthFactor),
                    $previousData['canceled']
                ),
                'past_due_growth' => $this->calculateGrowthPercentage(
                    round($lastPastDue * $growthFactor),
                    $previousData['past_due']
                ),
                'incomplete_growth' => $this->calculateGrowthPercentage(
                    round($lastIncomplete * $growthFactor),
                    $previousData['incomplete_expired']
                )
            ];
            
            $projections[] = $currentProjection;
            $previousData = $currentProjection;
        }

        return $projections;
    }

    private function calculateGrowthPercentage($current, $previous)
    {
        if ($previous == 0) return 0;
        return (($current - $previous) / $previous) * 100;
    }

    public function calculateSubscriptions()
    {
        // Modificar la consulta para sumar amount solo de suscripciones activas
        $rawData = Subscription::select(
            DB::raw('DATE_FORMAT(create_time, "%Y-%m") as month'),
            DB::raw('COUNT(*) as total'),
            DB::raw('SUM(CASE WHEN status = "active" THEN 1 ELSE 0 END) as active'),
            DB::raw('SUM(CASE WHEN status = "canceled" THEN 1 ELSE 0 END) as canceled'),
            DB::raw('SUM(CASE WHEN status = "past_due" THEN 1 ELSE 0 END) as past_due'),
            DB::raw('SUM(CASE WHEN status = "incomplete_expired" THEN 1 ELSE 0 END) as incomplete_expired'),
            DB::raw('SUM(CASE WHEN status = "active" THEN amount ELSE 0 END) as total_amount')
        )
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get()
            ->toArray();

        // Calcular porcentajes de crecimiento
        $this->subscriptionData = array_map(function($data, $index) use ($rawData) {
            $previousMonth = $index > 0 ? $rawData[$index - 1] : null;
            
            return array_merge($data, [
                'active_growth' => $previousMonth ? $this->calculateGrowthPercentage($data['active'], $previousMonth['active']) : 0,
                'canceled_growth' => $previousMonth ? $this->calculateGrowthPercentage($data['canceled'], $previousMonth['canceled']) : 0,
                'past_due_growth' => $previousMonth ? $this->calculateGrowthPercentage($data['past_due'], $previousMonth['past_due']) : 0,
                'incomplete_growth' => $previousMonth ? $this->calculateGrowthPercentage($data['incomplete_expired'], $previousMonth['incomplete_expired']) : 0,
            ]);
        }, $rawData, array_keys($rawData));

        // Calcular proyecciones solo si hay datos históricos
        if (!empty($this->subscriptionData)) {
            $this->projectedData = $this->calculateProjections($this->subscriptionData);
        }
    }

    public function render()
    {
        return view('livewire.subscription-projection');
    }
}