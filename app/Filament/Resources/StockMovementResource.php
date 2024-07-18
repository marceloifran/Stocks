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
use Sabberworm\CSS\Value\Size;
use App\Rules\GreaterThanStock;
use Filament\Resources\Resource;
use Filament\Forms\Components\Card;
use Filament\Tables\Filters\Filter;
use function Laravel\Prompts\select;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\StockMovementResource\Pages;

use Saade\FilamentAutograph\Forms\Components\SignaturePad;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use App\Filament\Resources\StockMovementResource\RelationManagers;

use Saade\FilamentAutograph\Forms\Components\Enums\DownloadableFormat;
use App\Filament\Resources\StockMovementResource\Widgets\StatsMovOverview;
use App\Filament\Resources\StockMovementResource\Widgets\StockMovementsChart;
// use Coolsam\SignaturePad\Forms\Components\Fields\SignaturePad;

class StockMovementResource extends Resource
{
    protected static ?string $model = StockMovement::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';
    protected static ?string $navigationLabel = 'Movements';
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
                ->label(trans('form.movement_quantity'))
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
                ->required(),
                Select::make('personal_id')
               ->options( personal::all()->pluck('nombre', 'id'))
                ->searchable()
                ->label('Personal')
                ->required(),
                Forms\Components\DatePicker::make('fecha_movimiento')
                ->autofocus()
                ->required()
                ->label(trans('form.movement_date'))
                ->default(Carbon::now())
               ,
               Forms\Components\Textarea::make('marca')
               ->autofocus()
               ->label(trans('form.brand'))
               ->nullable(),
            select::make('certificacion')
            ->options([
                'Si' => 'Si',
                'No ' => 'No',
            ])
            ->label(trans('form.certification'))
            ->nullable()
            ->searchable()
            ->default('Si'),
            SignaturePad::make('firma')
            ->required()
            ->label(trans('form.signature'))
            ->downloadableFormats([
                DownloadableFormat::PNG,
                DownloadableFormat::JPG,
                DownloadableFormat::SVG,
            ])
            ->backgroundColor('#FFFFFF')  // Background color on light mode
            ->backgroundColorOnDark('#FFFFFF')     // Background color on dark mode (defaults to backgroundColor)
            ->exportBackgroundColor('#FFFFFF')     // Background color on export (defaults to backgroundColor)
            ->penColor('#040404')                  // Pen color on light mode
            ->penColorOnDark('#040404')            // Pen color on dark mode (defaults to penColor)
            ->exportPenColor('#040404') ,
               select::make('tipo')
               ->options([
                   'Vaquetas' => 'Vaquetas',
                   'Latex' => 'Latex',
                   'Anticortes ' => 'Anticortes',
                   'Claras ' => 'Claras',
                   'Oscuras ' => 'Oscuras',
                   'Cuero ' => 'Cuero',
               ])
               ->label(trans('form.type'))
               ->nullable()
               ->searchable(),
                Forms\Components\Textarea::make('observaciones')
                ->autofocus()
                ->label(trans('form.observations'))
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
                 ->icon('heroicon-o-inbox-stack')
               ,
                Tables\Columns\TextColumn::make('cantidad_movimiento')
                ->searchable()
                ->label('Movement quantity')
                ->sortable()
               ,
                Tables\Columns\TextColumn::make('personal.nombre')
                ->searchable()
                ->sortable()
                ->icon('heroicon-o-user')

                ,
                Tables\Columns\TextColumn::make('fecha_movimiento')
                ->date('d/m/Y')
                ->label('Movement date')
                ->searchable()
                ->icon('heroicon-o-calendar-days')

                ->sortable()
               ,
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
                // Tables\Actions\ViewAction::make(),
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
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

    public static function getNavigationBadge(): ?string
    {
        return (string) StockMovement::count();
    }


    public static function getRelations(): array
    {
        return [

        ];
    }

    public static function getWidgets(): array
    {
        return [
            // StatsMovOverview::class,
            // StockMovementsChart::class
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
