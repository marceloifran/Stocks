<?php

namespace App\Filament\Resources\StockResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Text;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StockMovementRelationManager extends RelationManager
{
    protected static string $relationship = 'StockMovement';

    public function form(Form $form): Form
    {
        return $form
        ->schema([
            TextInput::make('personal_nombre')
                ->value($this->record->personal->nombre)
                ->label('Nombre de la Persona')
                ->disabled(), // Hace que el campo sea de solo lectura
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
        ->recordTitleAttribute('personal.nombre')
        ->columns([
            Tables\Columns\TextColumn::make('personal.nombre')
                ->label('Nombre'),
            Tables\Columns\TextColumn::make('cantidad_movimiento'),
            Tables\Columns\TextColumn::make('fecha_movimiento')
            ->dateTime('d/m/Y')
            ->searchable()
            ->sortable(),
        ])
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
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
