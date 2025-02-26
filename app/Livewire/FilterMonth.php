<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Transaction;
use Carbon\Carbon;

class FilterMonth extends Component
{
    public $month1; // Mes seleccionado para el primer mes
    public $year1;  // Año seleccionado para el primer mes
    public $month2; // Mes seleccionado para el segundo mes
    public $year2;  // Año seleccionado para el segundo mes

    public $transactions1; // Transacciones del primer mes
    public $transactions2; // Transacciones del segundo mes
    public $labels;        // Etiquetas para el gráfico
    public $data1;         // Datos para el gráfico (primer mes)
    public $data2;         // Datos para el gráfico (segundo mes)

    public function mount()
    {
        // Inicializar con el mes actual y el mes anterior
        $this->month1 = $this->month1 ?? now()->month;
        $this->year1 = $this->year1 ?? now()->year;
        $this->month2 = $this->month2 ?? now()->subMonth()->month;
        $this->year2 = $this->year2 ?? now()->subMonth()->year;

        $this->loadTransactions();
    }

    public function loadTransactions()
    {
        Carbon::setLocale('es');

        // Calcular fechas de inicio y fin para el primer mes
        $startDate1 = Carbon::create($this->year1, $this->month1, 1)->startOfMonth();
        $endDate1 = $startDate1->copy()->endOfMonth();

        // Calcular fechas de inicio y fin para el segundo mes
        $startDate2 = Carbon::create($this->year2, $this->month2, 1)->startOfMonth();
        $endDate2 = $startDate2->copy()->endOfMonth();

        // Cargar transacciones para el primer mes
        $this->transactions1 = Transaction::whereBetween('create_time', [$startDate1, $endDate1])->get();
        $totalAmount1 = $this->transactions1->sum('amount');

        // Cargar transacciones para el segundo mes
        $this->transactions2 = Transaction::whereBetween('create_time', [$startDate2, $endDate2])->get();
        $totalAmount2 = $this->transactions2->sum('amount');

        // Preparar datos para el gráfico
        $this->labels = [
            ucfirst($startDate1->translatedformat('F Y')),
            ucfirst($startDate2->translatedformat('F Y')),
        ];

        $this->data1 = [$totalAmount1, $totalAmount2];

        // Emitir un evento con los nuevos datos
        $this->dispatch('transactionsUpdated', labels: $this->labels, data: $this->data1);
    }


    public function render()
    {
        return view('livewire.filter-month');
    }
}
