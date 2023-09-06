<?php

namespace App\Filament\Resources;
use Closure;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use App\Models\stock;
use Filament\Forms\Get;
use App\Models\personal;
use Filament\Forms\Form;
use Filament\Tables\Table;

use App\Models\StockMovement;
use App\Rules\GreaterThanStock;
use Filament\Resources\Resource;
use Filament\Forms\Components\Card;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\StockMovementResource\Pages;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use App\Filament\Resources\StockMovementResource\RelationManagers;
use App\Filament\Resources\StockMovementResource\Widgets\StatsMovOverview;
use App\Filament\Resources\StockMovementResource\Widgets\StockMovementsChart;

class StockMovementResource extends Resource
{
    protected static ?string $model = StockMovement::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';
    protected static ?string $navigationLabel = 'Movimientos';
    protected static ?string $navigationGroup = 'Stocks';


    public static function form(Form $form): Form
    {

        return $form
            ->schema([
                Select::make('stock_id')
              ->options(stock::all()->pluck('nombre', 'id'))
                ->required()
                ->label('Stock')
                ->searchable()
                ->required(),
                Forms\Components\TextInput::make('cantidad_movimiento')
                ->autofocus()
                ->default(1)
                ->rules([
                    fn (Get $get): Closure => function ($attribute, $value, $fail) use ($get) {
                        if ($get('stock_id')) {
                            $stock = Stock::find($get('stock_id'));
                            if ($stock->cantidad < $value) {
                                $fail(__('La cantidad no puede ser mayor al stock'));
                            }
                        }
                    },
                ])
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
                ->sortable()
               ,
                Tables\Columns\TextColumn::make('cantidad_movimiento')
                ->searchable()
                ->sortable(),
                Tables\Columns\TextColumn::make('personal.nombre')
                ->searchable()
                ->sortable()
                ,
                Tables\Columns\TextColumn::make('fecha_movimiento')
                ->date('d/m/Y')
                ->searchable()
                ->sortable(),
                Tables\Columns\TextColumn::make('observaciones')
                ->searchable()
                ->sortable()
                ->toggleable(),

            ])->defaultSort('fecha_movimiento', 'desc')
            ->filters([
                Filter::make('created_at')
    ->form([
        Forms\Components\DatePicker::make('desde'),
        Forms\Components\DatePicker::make('hasta'),
    ])
    ->query(function (Builder $query, array $data): Builder {
        return $query
            ->when(
                $data['desde'],
                fn (Builder $query, $date): Builder => $query->whereDate('fecha_movimiento', '>=', $date),
            )
            ->when(
                $data['hasta'],
                fn (Builder $query, $date): Builder => $query->whereDate('fecha_movimiento', '<=', $date),
            );
    })
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            StockMovementsChart::class
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
