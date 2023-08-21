<?php

namespace App\Filament\Resources;
use App\Models\personal;
use App\Filament\Resources\StockMovementResource\Pages;
use App\Filament\Resources\StockMovementResource\RelationManagers;
use App\Filament\Resources\StockMovementResource\Widgets\StatsMovOverview;
use App\Models\StockMovement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StockMovementResource extends Resource
{
    protected static ?string $model = StockMovement::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';

    public static function form(Form $form): Form
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('stock.nombre')
                ->searchable()
                ->sortable(),
                Tables\Columns\TextColumn::make('cantidad_movimiento')
                ->searchable()
                ->sortable(),
                Tables\Columns\TextColumn::make('personal.nombre'),
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
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [

        ];
    }

    public static function getWidgets(): array
    {
        return [
            StatsMovOverview::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStockMovements::route('/'),
            'create' => Pages\CreateStockMovement::route('/create'),
            'edit' => Pages\EditStockMovement::route('/{record}/edit'),
        ];
    }
}
