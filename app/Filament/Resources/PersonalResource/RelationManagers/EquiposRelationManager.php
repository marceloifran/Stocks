<?php

namespace App\Filament\Resources\PersonalResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EquiposRelationManager extends RelationManager
{
    protected static string $relationship = 'equipos';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nombre')
            ->columns([
                Tables\Columns\TextColumn::make('personal.nombre')
                ->searchable()
                ->sortable(),
                Tables\Columns\TextColumn::make('fecha_ultimo_mantenimiento')
                ->searchable()
                ->sortable(),
                Tables\Columns\TextColumn::make('nombre')
                ->searchable()
                ->sortable(),
                Tables\Columns\TextColumn::make('tipo')
                ->searchable()
                ->sortable(),
                Tables\Columns\TextColumn::make('estado')
                ->searchable()
                ->sortable(),
                Tables\Columns\TextColumn::make('patente')
                ->searchable()
                ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
