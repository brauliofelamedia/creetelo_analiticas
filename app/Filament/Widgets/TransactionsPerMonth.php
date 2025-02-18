<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TransactionsPerMonth extends Widget
{
    protected static string $view = 'filament.widgets.transactions-per-month';

    public $year;

    public function mount()
    {
        $this->year = now()->year;
    }

    protected function getTransactionsData()
    {
        $transactions = Transaction::query()
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(amount) as total_amount')
            )
            ->whereYear('created_at', $this->year)
            ->where('status', 'succeeded')
            ->groupBy('month')
            ->get();

        $data = [];
        for ($month = 1; $month <= 12; $month++) {
            $data[$month] = $transactions->firstWhere('month', $month)?->total_amount ?? 0;
        }

        return $data;
    }
}
