<?php

namespace App\Filament\Resources\PersonalResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class StockMoventRelationManager extends RelationManager
{
    protected static string $relationship = 'stockMovement';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('cantidad_movimiento')
                ->autofocus()
                ->required()
                ->placeholder(__('Cantidad')),
                Select::make('stock_id')
                ->relationship('stock', 'nombre')
                ->required()
                ->searchable(),
                Select::make('personal_id')
                ->relationship('personal', 'nombre' )
                ->searchable()
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('personal_id')
            ->columns([
                Tables\Columns\TextColumn::make('cantidad_movimiento'),
                Tables\Columns\TextColumn::make('stock.nombre'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }
}
