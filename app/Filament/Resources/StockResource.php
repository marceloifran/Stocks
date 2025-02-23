<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\stock;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Illuminate\Contracts\View\View;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Route;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\Layout\Split;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\StockResource\Pages;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use App\Filament\Resources\StockResource\RelationManagers;
use App\Filament\Resources\StockResource\Widgets\StockChart;
use App\Filament\Resources\StockResource\Widgets\StockOverview;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use App\Models\User;
use App\Notifications\LowStockNotification;
use Illuminate\Support\Facades\Notification;

class StockResource extends Resource
{
    protected static ?string $model = Stock::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox-stack';
    protected static ?string $navigationLabel = 'Stock';
    protected static ?string $navigationGroup = 'Stocks';
    protected static  ?string $recordTitleAttribute = 'nombre';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->required()
                    ->label(trans('form.name'))
                    ->unique(ignoreRecord: true)
                    ->required(),
                Forms\Components\DatePicker::make('fecha')
                    ->label(trans('form.date'))
                    ->default(now())
                    ->required(),
                Forms\Components\TextInput::make('cantidad')
                    ->label(trans('form.quantity'))
                    ->required(),
                Forms\Components\TextInput::make('precio')
                    ->numeric()
                    ->label(trans('form.price'))
                    ->required(),
                Forms\Components\Textarea::make('descripcion')
                    ->required()
                    ->label(trans('form.description')),
                Select::make('tipo_stock')
                    ->options([
                        'Material' => 'Material',
                        'Consumible' => 'Consumible',
                    ])->searchable()
                    ->label(trans('form.stock_type'))
                    ->required(),
                Select::make('unidad_medida')
                    ->options([
                        'Kg' => 'Kg',
                        'Lts' => 'Lts',
                        'Mts' => 'Mts',
                        'Unidad' => 'Unidad',
                    ])->searchable()
                    ->label(trans('form.unit'))
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Split::make([
                    TextColumn::make('nombre')
                        ->searchable()
                        ->sortable()
                        ->label(trans('form.name'))
                        ->icon('heroicon-o-inbox-stack'),

                    TextColumn::make('cantidad')
                        ->searchable()
                        ->label(trans('form.quantity'))
                        ->sortable(),

                    TextColumn::make('fecha')
                        ->searchable()
                        ->label(trans('form.date'))
                        ->icon('heroicon-o-calendar-days')
                        ->sortable(),

                    TextColumn::make('is_low_stock')
                        ->label(trans('form.stock_state'))
                        ->badge()
                        ->color(function (Stock $record) {
                            return match ($record->is_low_stock) {
                                'Stock Alto' => 'success',
                                'Stock Medio' => 'warning',
                                'Stock Bajo' => 'danger',
                                default => 'warning',
                            };
                        })
                        ->copyable(),

                    TextColumn::make('valor_total')
                        ->label(trans('form.stock_value'))
                        ->icon('heroicon-o-currency-dollar')
                        ->money('arg')
                        ->getStateUsing(fn(Stock $record) => $record->valor_total)
                        ->sortable(),
                ])
                    ->from('md')
            ])
            ->filters([])
            ->actions([
                // Tables\Actions\Action::make('Ver Detalle')
                //     ->url(fn (Stock $record) => route('reporte.variacion_stock', $record->id))
                //     ->icon('heroicon-o-eye'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    ExportBulkAction::make()

                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // RelationManagers\StockHistoryRelationManager::class,
            // RelationManagers\StockMovementRelationManager::class,
        ];
    }


    public static function getWidgets(): array
    {
        return [];
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
