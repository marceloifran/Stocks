<?php

namespace App\Filament\Resources;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use App\Models\stock;
use App\Models\personal;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\StockMovement;
use Filament\Tables\Filters\Filter;

use Filament\Resources\Resource;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\StockMovementResource\Pages;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use App\Filament\Resources\StockMovementResource\RelationManagers;
use App\Filament\Resources\StockMovementResource\Widgets\StatsMovOverview;

class StockMovementResource extends Resource
{
    protected static ?string $model = StockMovement::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';
    protected static ?string $navigationLabel = 'Movimientos';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Select::make('stock_id')
              ->options( stock::all()->pluck('nombre', 'id'))
                ->required()
                ->label('Stock')
                ->searchable()
                ->required(),
                Forms\Components\TextInput::make('cantidad_movimiento')
                ->autofocus()
                ->required()
                ->default(1)
                ->placeholder(__('Cantidad'))
                ->required(),
                Select::make('personal_id')
               ->options( personal::all()->pluck('nombre', 'id'))
                ->searchable()
                ->label('Personal')
                ->required(),
                Forms\Components\DatePicker::make('fecha_movimiento')
                ->autofocus()
                ->required()
                ->default(Carbon::now())
               ,
                Forms\Components\Textarea::make('observaciones')
                ->autofocus()
                ->placeholder(__('Observaciones'))
                ->nullable(),

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
                Tables\Columns\TextColumn::make('personal.nombre')
                ->searchable()
                ->sortable(),
                Tables\Columns\TextColumn::make('fecha_movimiento')
                ->date('d/m/Y')
                ->searchable()
                ->sortable(),
                Tables\Columns\TextColumn::make('observaciones')
                ->searchable()
                ->sortable(),

            ])
            ->filters([
                Filter::make('created_at')
    ->form([
        Forms\Components\DatePicker::make('created_from'),
        Forms\Components\DatePicker::make('created_until'),
    ])
    ->query(function (Builder $query, array $data): Builder {
        return $query
            ->when(
                $data['created_from'],
                fn (Builder $query, $date): Builder => $query->whereDate('fecha', '>=', $date),
            )
            ->when(
                $data['created_until'],
                fn (Builder $query, $date): Builder => $query->whereDate('fecha', '<=', $date),
            );
    })
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    FilamentExportBulkAction::make('export')
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
