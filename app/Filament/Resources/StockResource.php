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


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                ->autofocus()
                ->required()
                ->placeholder(__('Nombre'))
                ->required(),
                Forms\Components\DatePicker::make('fecha')
                ->autofocus()
                ->required()
               ,
                Forms\Components\TextInput::make('cantidad')
                ->autofocus()
                ->required()
                ->placeholder(__('Cantidad'))
               ,
                Forms\Components\Textarea::make('descripcion')
                ->autofocus()
                ->required()
                ->placeholder(__('Descripcion')),
                Select::make('tipo_stock')
                ->options([
                    'Construccion' => 'Construccion' ,
                    'EPP' => 'EPP' ,
                ])->searchable()
                ->required(),
                Select::make('unidad_medida')
                ->options([
                    'Kg' => 'Kg' ,
                    'Lts' => 'Lts' ,
                    'Mts' => 'Mts' ,
                    'Unidad' => 'Unidad' ,
                ])->searchable()
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
                ->copyable(),
                Tables\Columns\TextColumn::make('descripcion')
                ->searchable()
                ->sortable()
                ,
                Tables\Columns\TextColumn::make('cantidad')
                ->searchable()
                ->sortable(),
                Tables\Columns\TextColumn::make('fecha')->since()
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
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\EditAction::make(),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStocks::route('/'),
            'create' => Pages\CreateStock::route('/create'),
            'edit' => Pages\EditStock::route('/{record}/edit'),
        ];
    }
}
