<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Actions\Action;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\DateColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\TransactionResource\Widgets\StatsOverview;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationLabel = 'Transacciones'; 

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('amount')
                    ->prefix('$')
                    ->suffix(' USD')
                    ->label('Monto')->sortable()->searchable(),
                TextColumn::make('status')
                    ->formatStateUsing(function (string $state) {
                        // Cambiar el texto según el estado
                        return match ($state) {
                            'succeeded' => 'Correcto',
                            'failed' => 'Fallido',
                        };
                    })
                    ->color(function (string $state) {
                        return match ($state) {
                            'succeeded' => 'success',
                            'failed' => 'danger',
                        };
                    })
                    ->label('Estado')
                    ->sortable()
                    ->searchable(),
                BooleanColumn::make('livemode')->label('Modo Live')->sortable(),
                TextColumn::make('entitySourceName')->label('Membresia')->sortable()->searchable(),
                TextColumn::make('create_time')
                    ->dateTime('d-m-Y')
                    ->label('Fecha de Creación')->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                //Tables\Actions\EditAction::make(),
                //Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('actualizarRegistros')
                ->label('Actualizar Registros')
                ->color('primary')
                ->icon('heroicon-o-arrow-path')
                ->action(function () {
                    try {
                        // Mostrar notificación de proceso iniciado
                        Notification::make()
                            ->title('Actualización en proceso')
                            ->body('La actualización de registros ha comenzado.')
                            ->info()
                            ->send();

                        // Realizar la petición GET
                        $response = Http::get('transactions.update');

                        if ($response->successful()) {
                            Notification::make()
                                ->title('Actualización completada')
                                ->body('Los registros se han actualizado correctamente.')
                                ->success()
                                ->send();
                        } else {
                            throw new \Exception('Error en la respuesta del servidor');
                        }
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Error')
                            ->body('Ha ocurrido un error durante la actualización: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                })
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageTransactions::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
