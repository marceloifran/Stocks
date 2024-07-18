<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\stock;
use App\Models\personal;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;

use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;

use App\Filament\Resources\StockResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\StockResource\RelationManagers;
use App\Filament\Resources\StockResource\Widgets\StockOverview;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;

class StockResource extends Resource
{
    protected static ?string $model = Stock::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox-stack';
    protected static ?string $navigationLabel = 'Stock';
    protected static ?string $navigationGroup = 'Stocks';



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                ->autofocus()
                ->required()
                ->label(trans('form.name'))
                ->unique(ignoreRecord:true)
                ->placeholder(__('Name'))
                ->required(),
                Forms\Components\DatePicker::make('fecha')
                ->autofocus()
                ->label(trans('form.date'))
                ->default(now())
                ->required()
               ,
                Forms\Components\TextInput::make('cantidad')
                ->autofocus()
                ->label(trans('form.quantity'))
                ->required()
               ,
               Forms\Components\TextInput::make('precio')
               ->numeric()
               ->label(trans('form.price'))
               ->required(),
                Forms\Components\Textarea::make('descripcion')
                ->autofocus()
                ->required()
                ->label(trans('form.description')),
                Select::make('tipo_stock')
                ->options([
                    'Construccion' => 'Construccion' ,
                    'EPP' => 'EPP' ,
                ])->searchable()
                ->label(trans('form.stock_type'))
                ->required(),
                Select::make('unidad_medida')
                ->options([
                    'Kg' => 'Kg' ,
                    'Lts' => 'Lts' ,
                    'Mts' => 'Mts' ,
                    'Unidad' => 'Unidad' ,
                ])->searchable()
                ->label(trans('form.unit'))
                ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                ->searchable()
                ->sortable()
                ->icon('heroicon-o-inbox-stack')
                ,
                Tables\Columns\TextColumn::make('cantidad')
                ->searchable()
                ->sortable(),
                Tables\Columns\TextColumn::make('fecha')
                ->searchable()
                ->icon('heroicon-o-calendar-days')
                ->sortable(),
                Tables\Columns\TextColumn::make('is_low_stock')
                ->label('Estado del Stock')
                ->badge()
                ->color(function(stock $record) {
                    return match ($record->is_low_stock) {
                        'Stock Alto' => 'success',
                        'Stock Medio' => 'warning',
                        'Stock Bajo' => 'danger',
                        default => 'warning',
                    };
                })
                ->sortable(),
                Tables\Columns\TextColumn::make('valor_total')
                ->label('Valor del Stock')
                ->icon('heroicon-o-currency-dollar')
                ->money('arg')
                ->getStateUsing(fn (Stock $record) => $record->valor_total)
                ->sortable(),
                // Tables\Columns\TextColumn::make('tipo_stock')
                // ->searchable()
                // ->badge()
                // ->color(function(stock $record) {
                //     return match ($record->tipo_stock) {
                //         'Construccion' => 'warning',
                //         'EPP' => 'success',
                //         default => 'danger',
                //     };
                // })

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
                // Tables\Actions\ViewAction::make(),
                // Tables\Actions\DeleteAction::make(),
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    FilamentExportBulkAction::make('export')
                        ,
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\StockHistoryRelationManager::class,
            RelationManagers\StockMovementRelationManager::class,
        ];
    }

    public static function getWidgets(): array
    {
        return [
            StockOverview::class,
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) stock::count();
    }

    public static function getPages(): array
    {
        return [
             'index' => Pages\ListStocks::route('/'),
            'create' => Pages\CreateStock::route('/create'),
            'edit' => Pages\EditStock::route('/{record}/edit'),
        ];
    }
}
