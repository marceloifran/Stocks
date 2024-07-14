<?php

namespace App\Filament\Resources\PersonalResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use App\Models\personal;
use Filament\Forms\Form;
use App\Models\asistencia;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;

class AsistenciaRelationManager extends RelationManager
{
    protected static string $relationship = 'asistencia';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('codigo')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
        ->recordTitleAttribute('codigo')
        ->columns([
            Tables\Columns\TextColumn::make('codigo')
            ,
            Tables\Columns\TextColumn::make('fecha'),
            Tables\Columns\TextColumn::make('hora'),
            Tables\Columns\TextColumn::make('estado'),


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
                    // Tables\Actions\DeleteBulkAction::make(),
                    FilamentExportBulkAction::make('export')

                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }
}
