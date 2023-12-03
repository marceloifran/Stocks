<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use App\Models\proyecto;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ProyectoResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ProyectoResource\RelationManagers;

class ProyectoResource extends Resource
{
    protected static ?string $model = proyecto::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationGroup = 'Management';
    protected static ?string $navigationLabel = 'Projects';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\TextInput::make('nombre')
                ->autofocus()
                ->required()
                ->placeholder(__('Nombre')),
                Forms\Components\Textarea::make('descripcion')
                ->autofocus()
                ->required()
                ->placeholder(__('Descripcion')),
                Forms\Components\DatePicker::make('fecha_inicio')
                ->autofocus()
                ->label('Fecha de Inicio')
                ->required()
                ->default(Carbon::now())
               ,
               Forms\Components\DatePicker::make('fecha_fin')
               ->autofocus()
               ->required()
               ->label('Fecha de Fin')
               ->default(Carbon::now()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                ->searchable()
                ->sortable()
               ,
                Tables\Columns\TextColumn::make('fecha_inicio')
                ->searchable()
                ->sortable()
               ,
                Tables\Columns\TextColumn::make('fecha_fin')
                ->searchable()
                ->sortable()
               ,
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
            'index' => Pages\ListProyectos::route('/'),
            'create' => Pages\CreateProyecto::route('/create'),
            'edit' => Pages\EditProyecto::route('/{record}/edit'),
        ];
    }
}
