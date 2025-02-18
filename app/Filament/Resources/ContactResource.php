<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactResource\Pages;
use App\Filament\Resources\ContactResource\RelationManagers;
use App\Models\Contact;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Columns\TagsColumn;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Http;
use Filament\Notifications\Notification;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use App\Helpers\CountryHelper;
use Carbon\Carbon;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Select;
use Filament\Tables\Enums\FiltersLayout;

class ContactResource extends Resource
{
    protected static ?string $model = Contact::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        $assignedCountries = Contact::distinct('country')->pluck('country')->toArray();
        $filteredCountries = array_intersect_key(CountryHelper::COUNTRIES, array_flip($assignedCountries));

        return $table
            ->columns([
                TextColumn::make('fullName')
                    ->searchable(['firstNameLowerCase', 'lastNameLowerCase'])
                    ->label('Nombre'),
                TextColumn::make('email')
                    ->label('Correo'),
                TextColumn::make('phone')
                    ->label('Teléfono'),
                TextColumn::make('country')
                    ->label('País'),
                TextColumn::make('country')
                    ->formatStateUsing(function ($state) {
                        return CountryHelper::COUNTRIES[$state];
                    })
                    ->label('País'),
                TextColumn::make('dateAdded')
                    ->sortable()
                    ->label('Fecha de registro'),
                TextColumn::make('dateUpdated')
                    ->sortable()
                    ->label('Última actualización')
                    ->formatStateUsing(function ($state) {
                        return Carbon::parse($state)->diffForHumans();
                    })
            ])
            ->defaultSort('dateAdded', 'desc')
            ->filters([
                SelectFilter::make('tags')
                    ->label('Etiquetas')
                    ->options([
                        'wowfriday_ plan anual' => 'WowFriday Plan Anual',
                        'wowfriday_plan mensual' => 'WowFriday Plan Mensual',
                    ])
                    ->query(function ($query, $data) {
                        if ($data['value']) {
                            $query->whereJsonContains('tags', $data['value']);
                        }
                    }),
                SelectFilter::make('country')
                    ->label('País')
                    ->options($filteredCountries)
                    ->searchable()
                    ->placeholder('Selecciona un país'),
                    Filter::make('dateAdded')
                    ->form([
                        Select::make('data')
                            ->label('Fecha de registro')
                            ->options([
                                'today' => 'Hoy',
                                'last_7_days' => 'Últimos 7 días',
                                'last_30_days' => 'Últimos 30 días',
                                'last_year' => 'Último año',
                            ]),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (isset($data['data'])) {
                            $period = $data['data'];
                            $now = now();
                            switch ($period) {
                                case 'today':
                                    $query->whereDate('dateAdded', $now->toDateString());
                                    break;
                                case 'last_7_days':
                                    $query->whereBetween('dateAdded', [$now->copy()->subDays(7), $now]);
                                    break;
                                case 'last_30_days':
                                    $query->whereBetween('dateAdded', [$now->copy()->subDays(30), $now]);
                                    break;
                                case 'last_year':
                                    $query->whereBetween('dateAdded', [$now->copy()->subYear(), $now]);
                                    break;
                            }
                        }
                    }),
                Filter::make('dateUpdated')
                    ->form([
                        Select::make('period')
                            ->label('Última actualización')
                            ->options([
                                'today' => 'Hoy',
                                'last_7_days' => 'Últimos 7 días',
                                'last_30_days' => 'Últimos 30 días',
                                'last_year' => 'Último año',
                            ]),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (isset($data['period'])) {
                            $period = $data['period'];
                            $now = now();
                            switch ($period) {
                                case 'today':
                                    $query->whereDate('dateUpdated', $now->toDateString());
                                    break;
                                case 'last_7_days':
                                    $query->whereBetween('dateUpdated', [$now->subDays(7), $now]);
                                    break;
                                case 'last_30_days':
                                    $query->whereBetween('dateUpdated', [$now->subDays(30), $now]);
                                    break;
                                case 'last_year':
                                    $query->whereBetween('dateUpdated', [$now->subYear(), $now]);
                                    break;
                            }
                        }
                    })
            ], layout: FiltersLayout::AboveContent)
            ->headerActions([
                Action::make('custom_action')
                    ->label('Actualizar contactos')
                    ->color('primary')
                    ->icon('heroicon-o-arrow-path')
                    ->action(function () {

                        try {
                            // Lógica para actualizar contactos
                            $response = Http::withOptions(['verify' => false])->get(route('contact.insert'));
                
                            if ($response->successful()) {
                                Notification::make()
                                    ->title('Éxito')
                                    ->body('Se han sincronizado los contactos')
                                    ->success()
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title('Error en la solicitud')
                                    ->body('La API devolvió un error: ' . $response->status())
                                    ->danger()
                                    ->send();
                            }
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Error en la solicitud')
                                ->body('Hubo un error al intentar hacer la solicitud: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->actions([
                Action::make('view')
                    ->label('Ver')
                    ->modalHeading('Información del registro')
                    ->modalButton(false)
                    ->modalContent(function ($record) {
                        return view('ContactResource.modal', [
                            'record' => $record,
                        ]);
                    })
                    ->modalActions(fn () => [])
                    ->icon('heroicon-o-eye'),
                //Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                ExportBulkAction::make(),
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageContacts::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
