<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\capacitaciones;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CapacitacionResource\Pages;
use App\Filament\Resources\CapacitacionResource\RelationManagers;

class CapacitacionResource extends Resource
{
    protected static ?string $model = capacitaciones::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Personal';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('fecha')
                    ->label('Fecha de la Capacitación')
                    ->required(),
                Forms\Components\TextInput::make('tematica')
                    ->label('Temática')
                    ->required(),
                Forms\Components\TextInput::make('capacitador')
                    ->label('Nombre del Capacitador')
                    ->required(),
                Select::make('personal')
                    ->label('Personal Capacitado')
                    ->multiple()
                    ->relationship('personal', 'nombre')
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('modalidad')
                    ->label('Modalidad de la Capacitación')
                    ->options([
                        'presencial' => 'Presencial',
                        'virtual' => 'Virtual',
                        'sincrono' => 'Sincrónico',
                        'asincrono' => 'Asincrónico',
                    ])
                    
                    ->required(),
                Forms\Components\Textarea::make('observaciones')
                    ->label('Observaciones'),
            ]);
    }



    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('fecha')->label('Fecha de la Capacitación'),
                Tables\Columns\TextColumn::make('tematica')->label('Temática'),
                Tables\Columns\TextColumn::make('capacitador')->label('Nombre del Capacitador'),
                Tables\Columns\TextColumn::make('modalidad')->label('Modalidad'),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListCapacitacions::route('/'),
            'create' => Pages\CreateCapacitacion::route('/create'),
            'edit' => Pages\EditCapacitacion::route('/{record}/edit'),
        ];
    }
}
