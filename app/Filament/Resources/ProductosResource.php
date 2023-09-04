<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use App\Models\producto;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Cheesegrits\FilamentGoogleMaps\Fields\Map;
use App\Filament\Resources\ProductosResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ProductosResource\RelationManagers;

class ProductosResource extends Resource
{
    protected static ?string $model = producto::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationGroup = 'Inventario';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('codigo_barras')
                ->autofocus()
                ->required()
                ->unique()
                ->placeholder(__('Codigo'))
                ->required()
               ,
                  Forms\Components\TextInput::make('Nombre')
                  ->autofocus()
                  ->required()
                  ->placeholder(__('Nombre'))
                  ->required()
                 ,
                  Forms\Components\TextInput::make('cantidad_stock')
                  ->autofocus()
                  ->required()
                  ->numeric()
                  ->placeholder(__('Stock'))
                  ->required()
                 ,
                  Forms\Components\TextInput::make('cantidad_minima')
                  ->autofocus()
                  ->numeric()
                  ->required()
                  ->placeholder(__('Stock Minimo'))
                  ->required()
                 ,
                  Forms\Components\TextInput::make('precio_compra')
                  ->autofocus()
                  ->numeric()
                  ->required()
                  ->placeholder(__('Precio de Compra'))
                  ->required()
                 ,
                  Forms\Components\TextInput::make('precio_venta')
                  ->autofocus()
                  ->numeric()
                  ->required()
                  ->placeholder(__('Precio de Venta'))
                  ->required()
                 ,
                 Forms\Components\Textarea::make('descripcion')
                 ->autofocus()
                 ->placeholder(__('Descripcion'))
                 ->nullable(),
                  Forms\Components\DatePicker::make('fecha_vencimiento')
                  ->autofocus()
                  ->required()
                  ->default(Carbon::now())
                 ,
                 Forms\Components\TextInput::make('proveedor')
                 ->autofocus()
                 ->required()
                 ->placeholder(__('Proveedor'))
                 ->required()
                ,
                 Forms\Components\TextInput::make('ubicacion_almacen')
                 ->autofocus()
                 ->required()
                 ->placeholder(__('Ubicacion del Almacen'))
                 ->required()
                ,
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductos::route('/'),
            'create' => Pages\CreateProductos::route('/create'),
            'edit' => Pages\EditProductos::route('/{record}/edit'),
        ];
    }
}
