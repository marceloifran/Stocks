<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SueldoResource\Pages;
use App\Models\Sueldo;
use App\Models\asistencia;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class SueldoResource extends Resource
{
    protected static ?string $model = Sueldo::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('personal_id')
                    ->relationship('personal', 'id')
                    ->required(),
                Forms\Components\DatePicker::make('fecha')
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                        $personalId = $get('personal_id');
                        $fecha = $state;

                        $asistencias = asistencia::where('codigo', $personalId)
                            ->whereDate('fecha', $fecha)
                            ->get();

                        $horas = Sueldo::calcularHoras($asistencias);

                        $set('horas_normales', $horas['horas_normales']);
                        $set('horas_extras', $horas['horas_extras']);
                    }),
                Forms\Components\TextInput::make('horas_normales')->required()->disabled(),
                Forms\Components\TextInput::make('horas_extras')->required()->disabled(),
                Forms\Components\TextInput::make('pago_horas_normales')->required(),
                Forms\Components\TextInput::make('pago_horas_extras')->required(),
                Forms\Components\TextInput::make('total')
                    ->required()
                    ->afterStateUpdated(function ($state, callable $set, $get) {
                        $total = ($get('horas_normales') * $get('pago_horas_normales')) + ($get('horas_extras') * $get('pago_horas_extras'));
                        $set('total', $total);
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('personal.id'),
                TextColumn::make('fecha')->date(),
                TextColumn::make('horas_normales'),
                TextColumn::make('horas_extras'),
                TextColumn::make('pago_horas_normales'),
                TextColumn::make('pago_horas_extras'),
                TextColumn::make('total'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSueldos::route('/'),
            'create' => Pages\CreateSueldo::route('/create'),
            'edit' => Pages\EditSueldo::route('/{record}/edit'),
        ];
    }
}
