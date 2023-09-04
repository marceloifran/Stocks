<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MovementsProductsResource\Pages;
use App\Filament\Resources\MovementsProductsResource\RelationManagers;
use App\Models\MovementsProducts;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MovementsProductsResource extends Resource
{
    protected static ?string $model = MovementsProducts::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationGroup = 'Inventario';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
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
            'index' => Pages\ListMovementsProducts::route('/'),
            'create' => Pages\CreateMovementsProducts::route('/create'),
            'edit' => Pages\EditMovementsProducts::route('/{record}/edit'),
        ];
    }
}
