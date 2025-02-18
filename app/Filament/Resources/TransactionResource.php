<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\DateColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\TransactionResource\Widgets\StatsOverview;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageTransactions::route('/'),
        ];
    }
}
