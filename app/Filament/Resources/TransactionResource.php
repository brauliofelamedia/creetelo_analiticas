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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('currency')
                    ->maxLength(255),
                Forms\Components\TextInput::make('amount')
                    ->numeric(),
                Forms\Components\TextInput::make('status')
                    ->maxLength(255),
                Forms\Components\TextInput::make('livemode')
                    ->required()
                    ->maxLength(255)
                    ->default(0),
                Forms\Components\TextInput::make('entity_type')
                    ->maxLength(255),
                Forms\Components\TextInput::make('entity_source_type')
                    ->maxLength(255),
                Forms\Components\TextInput::make('entity_id')
                    ->maxLength(255),
                Forms\Components\TextInput::make('subscription_id')
                    ->maxLength(255),
                Forms\Components\TextInput::make('charge_id')
                    ->maxLength(255),
                Forms\Components\TextInput::make('source_type')
                    ->maxLength(255),
                Forms\Components\TextInput::make('state')
                    ->maxLength(255),
                Forms\Components\TextInput::make('summary')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('entitySourceName')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('create_time')
                    ->required(),
                Forms\Components\TextInput::make('contact_id')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('currency')
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('livemode')
                    ->searchable(),
                Tables\Columns\TextColumn::make('entity_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('entity_source_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('entity_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('subscription_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('charge_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('source_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('state')
                    ->searchable(),
                Tables\Columns\TextColumn::make('summary')
                    ->searchable(),
                Tables\Columns\TextColumn::make('entitySourceName')
                    ->searchable(),
                Tables\Columns\TextColumn::make('create_time')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('contact_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}
