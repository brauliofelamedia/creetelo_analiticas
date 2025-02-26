<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use App\Models\Transaction;
use Filament\Resources\Pages\Page;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
class SortTransaction extends Page
{
    protected static string $resource = TransactionResource::class;

    protected static string $view = 'filament.resources.transaction-resource.pages.sort-transaction';

    public $startDate;
    public $endDate;

    public function mount($start_date = null, $end_date = null)
    {
        $this->startDate = ($start_date)? Carbon::parse($start_date) : Carbon::now()->startOfMonth();
        $this->endDate = ($end_date)? Carbon::parse($end_date) : Carbon::now();
    }

    // Alternativa manual si lo anterior no funciona
    public function updateFilter()
    {
        // Este método es solo para forzar una actualización de la vista
        $this->dispatch('refresh');
    }

    public function getTransactionDataProperty()
    {
        // Asegurar que las fechas sean objetos Carbon
        $startDate = ($this->startDate) ? $this->startDate : Carbon::parse($this->startDate);
        $endDate =($this->endDate) ? $this->endDate : Carbon::parse($this->endDate);
        
        // Asegurar que endDate sea el final del día
        $endDate = $endDate->copy()->endOfDay();
        
        // Depuración - útil para verificar qué fechas se están usando
        $transactions = Transaction::query()
            ->select(DB::raw('DATE(create_time) as date'), DB::raw('SUM(amount) as total_amount'))
            ->whereBetween('create_time', [$startDate, $endDate]) 
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Preparar los datos para el gráfico
        $labels = $transactions->pluck('date')->toArray();
        $amounts = $transactions->pluck('total_amount')->toArray();

        return [
            'transactions' => $transactions,
            'labels' => $labels,
            'amounts' => $amounts,
        ];
    }
}
