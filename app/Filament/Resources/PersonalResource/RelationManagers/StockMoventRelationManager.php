<?php

namespace App\Filament\Resources\PersonalResource\RelationManagers;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use App\Models\stock;
use App\Models\personal;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class StockMoventRelationManager extends RelationManager
{
    protected static string $relationship = 'stockMovement';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('cantidad_movimiento')
                ->autofocus()
                ->required()
                ->placeholder(__('Cantidad')),
                Select::make('stock_id')
                ->options(stock::all()->pluck('nombre', 'id'))
                  ->required()
                  ->label('Stock')
                  ->searchable()
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

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('personal_id')
            ->columns([
                Tables\Columns\TextColumn::make('cantidad_movimiento')
                ,
                Tables\Columns\TextColumn::make('stock.nombre'),
                Tables\Columns\TextColumn::make('fecha_movimiento')
                ->date('d/m/Y')
                ,

            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
}
