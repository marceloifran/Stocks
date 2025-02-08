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
use Filament\Resources\Resource;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Card;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Columns\Layout\Split;
use function Laravel\Prompts\select;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\StockMovementResource\Pages;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use Saade\FilamentAutograph\Forms\Components\SignaturePad;
use App\Filament\Resources\StockMovementResource\RelationManagers;
use Saade\FilamentAutograph\Forms\Components\Enums\DownloadableFormat;
// use Coolsam\SignaturePad\Forms\Components\Fields\SignaturePad;

class StockMovementResource extends Resource
{
    protected static ?string $model = StockMovement::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';
    protected static ?string $navigationLabel = 'Movements';
    protected static ?string $navigationGroup = 'Stocks';
    protected static  ?string $recordTitleAttribute = 'stock.nombre';



    public static function form(Form $form): Form
    {

        return $form
            ->schema([
                Select::make('stock_id')
                    ->options(stock::query()->pluck('nombre', 'id'))
                    ->required()
                    ->label('Stock')
                    ->searchable()
                    ->required(),
                Forms\Components\TextInput::make('cantidad_movimiento')
                    ->autofocus()
                    ->default(1)
                    ->label(trans('form.movement_quantity'))
                    ->rules([
                        fn(Get $get): Closure => function ($attribute, $value, $fail) use ($get) {
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
                    ->options(personal::query()->pluck('nombre', 'id'))
                    ->searchable()
                    ->label('Personal')
                    ->required(),
                Forms\Components\DateTimePicker::make('fecha_movimiento')
                    ->autofocus()
                    ->required()
                    ->label(trans('form.movement_date'))
                    ->default(Carbon::now()) // Guarda fecha + hora actual
                    ->seconds(true) // ✅ Permite guardar segundos
                    ->format('Y-m-d H:i:s') // ✅ Formato con segundos
                    ->timezone('America/Argentina/Buenos_Aires'), // Opcional: Ajustar zona horaria

                Forms\Components\Textarea::make('marca')
                    ->autofocus()
                    //preguntar a la mama la marca tipica de calzado para poner 
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
                Forms\Components\DatePicker::make('fecha_vencimiento')
                    ->autofocus()
                    ->nullable()
                    ->default(null)
                    ->label(trans('form.movement_cad'))
                ,
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
                    ->exportPenColor('#040404'),
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
                Split::make([
                    TextColumn::make('stock.nombre')
                    ->label(trans('tables.stock'))
                        ->searchable()
                        ->icon('heroicon-o-inbox-stack'),
                
                    TextColumn::make('cantidad_movimiento')
                        ->searchable()
                        ->label(trans('tables.movement_quantity')),
                
                    TextColumn::make('personal.nombre')
                        ->searchable()
                        ->icon('heroicon-o-user'),
                
                    TextColumn::make('fecha_movimiento')
                        ->date('d/m/Y')
                        ->label(trans('tables.movement_date'))
                        ->searchable()
                        ->icon('heroicon-o-calendar-days'),
                
                    TextColumn::make('fecha_vencimiento')
                        ->date('d/m/Y')
                        ->label(trans('form.movement_cad'))
                        ->icon('heroicon-o-calendar-days')
                        ->badge()
                        ->color(function (StockMovement $stockMovement) {
                            if (!$stockMovement->fecha_vencimiento) {
                                return 'secondary'; // Si no hay fecha de vencimiento
                            }
                
                            $hoy = now();
                            $vencimiento = \Carbon\Carbon::parse($stockMovement->fecha_vencimiento);
                            $diferenciaDias = $hoy->diffInDays($vencimiento, false); // false para incluir negativos
                
                            if ($diferenciaDias < 0) {
                                return 'danger'; // Vencido
                            } elseif ($diferenciaDias >= 0 && $diferenciaDias <= 7) {
                                return 'danger'; // Menos de 7 días (mañana inclusive)
                            } elseif ($diferenciaDias > 7 && $diferenciaDias <= 30) {
                                return 'warning'; // Entre 7 y 30 días
                            } else {
                                return 'success'; // Más de 30 días
                            }
                        })
                ])
                    ->from('md')
            ])->defaultSort('fecha_movimiento', 'desc')
            ->filters([
                Filter::make('fecha_movimiento')
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

    public static function getNavigationBadge(): ?string
    {
        return (string) StockMovement::count();
    }


    public static function getRelations(): array
    {
        return [];
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
