<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Contact;
use Carbon\Carbon;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        // Obtener el mes y año actual
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Obtener el mes y año del mes anterior del año pasado
        $previousYear = $currentYear - 1;
        $previousMonth = $currentMonth - 1;
        if ($previousMonth === 0) {
            $previousMonth = 12;
            $previousYear -= 1;
        }

        // Datos del mes actual
        $contactMonthCurrent = Contact::whereYear('dateAdded', $currentYear)
            ->whereMonth('dateAdded', $currentMonth)
            ->count();

        $mensualCurrent = Contact::whereYear('dateAdded', $currentYear)
            ->whereMonth('dateAdded', $currentMonth)
            ->whereRaw("JSON_CONTAINS(tags, '\"wowfriday_plan mensual\"')")
            ->count();

        $anualCurrent = Contact::whereYear('dateAdded', $currentYear)
            ->whereMonth('dateAdded', $currentMonth)
            ->whereRaw("JSON_CONTAINS(tags, '\"wowfriday_plan anual\"')")
            ->count();

        // Datos del mes anterior del año pasado
        $contactMonthPrevious = Contact::whereYear('dateAdded', $previousYear)
            ->whereMonth('dateAdded', $previousMonth)
            ->count();

        $mensualPrevious = Contact::whereYear('dateAdded', $previousYear)
            ->whereMonth('dateAdded', $previousMonth)
            ->whereRaw("JSON_CONTAINS(tags, '\"wowfriday_plan mensual\"')")
            ->count();

        $anualPrevious = Contact::whereYear('dateAdded', $previousYear)
            ->whereMonth('dateAdded', $previousMonth)
            ->whereRaw("JSON_CONTAINS(tags, '\"wowfriday_plan anual\"')")
            ->count();

        // Calcular diferencias
        $contactDifference = $contactMonthCurrent - $contactMonthPrevious;
        $mensualDifference = $mensualCurrent - $mensualPrevious;
        $anualDifference = $anualCurrent - $anualPrevious;

        // Formatear diferencias para mostrar en la descripción
        $formatDifference = fn ($difference) => ($difference >= 0 ? "+$difference" : "$difference");

        return [
            Stat::make('Contactos este mes', $contactMonthCurrent)
                ->description($formatDifference($contactDifference) . ' vs mes anterior del año pasado')
                ->descriptionIcon($contactDifference >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($contactDifference >= 0 ? 'success' : 'danger'),

            Stat::make('Usuarios con membresía mensual', $mensualCurrent)
                ->description($formatDifference($mensualDifference) . ' vs mes anterior del año pasado')
                ->descriptionIcon($mensualDifference >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($mensualDifference >= 0 ? 'success' : 'danger'),

            Stat::make('Usuarios con membresía anual', $anualCurrent)
                ->description($formatDifference($anualDifference) . ' vs mes anterior del año pasado')
                ->descriptionIcon($anualDifference >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($anualDifference >= 0 ? 'success' : 'danger'),
        ];
    }
}
