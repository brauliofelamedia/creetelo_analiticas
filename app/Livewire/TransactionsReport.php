<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Transaction;
use Carbon\Carbon;

class TransactionsReport extends Component
{
    public $startDate;
    public $endDate;
    public $showProfitLoss = false;
    public $transactions;
    public $labels;
    public $data;

    public function mount()
    {
        $this->startDate = $this->startDate ?? now()->startOfMonth()->toDateString();
        $this->endDate = $this->endDate ?? now()->endOfMonth()->toDateString();
        $this->loadTransactions();
    }

    public function loadTransactions()
    {
        $this->transactions = Transaction::whereBetween('create_time', [$this->startDate, $this->endDate])->get();

       // Agrupar transacciones por día y sumar los montos
        $groupedByDay = $this->transactions->groupBy(function ($transaction) {
            // Asegurarnos de que create_time sea un objeto Carbon y extraer solo la fecha
            return Carbon::parse($transaction->create_time)->format('Y-m-d');
        });

        // Preparar datos para el gráfico
        $this->labels = $groupedByDay->keys();
        $this->data = $groupedByDay->map(function ($dayTransactions) {
            return $dayTransactions->sum('amount');
        })->values();

        // Emitir un evento con los nuevos datos
        $this->dispatch('transactionsUpdated', labels : $this->labels,data: $this->data);
    }

    public function render()
    {
        return view('livewire.transactions-report');
    }
}
