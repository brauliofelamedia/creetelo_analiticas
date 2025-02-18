<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use App\Models\Contact;
use Illuminate\Database\Eloquent\Factories\Factory;
use Filament\Support\Colors\Color;
use Carbon\Carbon;
use DB;

class ContactsPerMonthChart extends ChartWidget
{
    protected static ?string $heading = 'Registros mensuales de {WowFriday mensual}';

    protected function getData(): array
    {
        $firstContactDate = Contact::min('dateAdded');
        $filteredContacts = Contact::whereRaw("JSON_CONTAINS(tags, '\"wowfriday_plan mensual\"')");

        // Obtener el año actual y el año anterior
        $currentYear = now()->year;
        $previousYear = $currentYear - 1;

        // Obtener los datos para el año actual
        $currentYearData = Trend::query($filteredContacts)
            ->dateColumn('dateAdded')
            ->between(
                start: Carbon::create($currentYear, 1, 1)->startOfMonth(),
                end: Carbon::create($currentYear, 12, 31)->endOfMonth(),
            )
            ->perMonth()
            ->count();

        // Obtener los datos para el año anterior
        $previousYearData = Trend::query($filteredContacts)
            ->dateColumn('dateAdded')
            ->between(
                start: Carbon::create($previousYear, 1, 1)->startOfMonth(),
                end: Carbon::create($previousYear, 12, 31)->endOfMonth(),
            )
            ->perMonth()
            ->count();

        // Mapear los datos para el año actual
        $currentYearDataset = [
            'label' => 'Año Actual',
            'data' => $currentYearData->map(fn (TrendValue $value) => $value->aggregate),
            'borderColor' => '#ea84fb',
            'backgroundColor' => '#ea84fb',
            'fill' => true,
        ];

        // Mapear los datos para el año anterior
        $previousYearDataset = [
            'label' => 'Año Anterior',
            'data' => $previousYearData->map(fn (TrendValue $value) => $value->aggregate),
            'borderColor' => '#7b8a8b',
            'backgroundColor' => '#7b8a8b',
            'fill' => true,
        ];

        // Obtener las etiquetas (meses) para el gráfico
        $labels = $currentYearData->map(fn (TrendValue $value) => Carbon::parse($value->date)->format('m-Y'));

        return [
            'datasets' => [
                $currentYearDataset,
                $previousYearDataset,
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
