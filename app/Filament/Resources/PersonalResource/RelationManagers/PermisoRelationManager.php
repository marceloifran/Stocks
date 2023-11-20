<?php

namespace App\Filament\Resources\PersonalResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use App\Models\permiso;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;

class PermisoRelationManager extends RelationManager
{
    protected static string $relationship = 'permiso';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tables\Columns\TextColumn::make('tipo')
                ->searchable()
                ->label('Actividad')
                ->sortable(),
                Tables\Columns\TextColumn::make('descripcion')
                ->searchable()
                ->label('Sector')
                ->sortable(),
                Tables\Columns\TextColumn::make('fecha')
                ->searchable()
                ->sortable(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('tipo')
            ->columns([
                Tables\Columns\TextColumn::make('tipo')
                ->searchable()
                ->label('Actividad')
                ->sortable(),
                Tables\Columns\TextColumn::make('descripcion')
                ->searchable()
                ->label('Sector')
                ->sortable(),
                Tables\Columns\TextColumn::make('fecha')
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
                    FilamentExportBulkAction::make('export')
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }
}
