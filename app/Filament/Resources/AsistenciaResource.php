<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\asistencia;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\AsistenciaResource\Pages;
use App\Filament\Resources\AsistenciaResource\RelationManagers;

class AsistenciaResource extends Resource
{
    protected static ?string $model = asistencia::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    // public static function form(Form $form): Form
    // {
    //     return $form
    //         ->schema([
    //             //
    //         ]);
    // }

    // public static function table(Table $table): Table
    // {
    //     return $table
    //         ->columns([
    //             Tables\Columns\TextColumn::make('fecha')
    //             ->searchable()
    //             ->sortable(),
    //             Tables\Columns\TextColumn::make('hora')
    //             ->searchable()
    //             ->sortable(),
    //             Tables\Columns\TextColumn::make('codigo')
    //             ->searchable()
    //             ->sortable(),
    //         ])
    //         ->groups([
    //             'fecha'
    //         ])
    //         ->filters([
    //             //
    //         ])
    //         ->actions([
    //             Tables\Actions\EditAction::make(),
    //             Action::make('Ver')->icon('heroicon-o-eye')->url(fn($record) => route('asistencia.show', $record)),

    //         ])
    //         ->bulkActions([
    //             Tables\Actions\BulkActionGroup::make([
    //                 Tables\Actions\DeleteBulkAction::make(),
    //             ]),
    //         ])
    //         ->emptyStateActions([
    //             Tables\Actions\CreateAction::make(),
    //         ]);
    // }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAsistencias::route('/'),
            'create' => Pages\CreateAsistencia::route('/create'),
            'edit' => Pages\EditAsistencia::route('/{record}/edit'),
        ];
    }
}
