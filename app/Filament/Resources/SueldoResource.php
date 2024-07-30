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
                    ->relationship('personal', 'nombre') // Cambiar 'id' por 'name'
                    ->required(),
                Forms\Components\Select::make('mes')
                    ->options([
                        '1' => 'Enero',
                        '2' => 'Febrero',
                        '3' => 'Marzo',
                        '4' => 'Abril',
                        '5' => 'Mayo',
                        '6' => 'Junio',
                        '7' => 'Julio',
                        '8' => 'Agosto',
                        '9' => 'Septiembre',
                        '10' => 'Octubre',
                        '11' => 'Noviembre',
                        '12' => 'Diciembre',
                    ])
                    ->required()
                    ->label('Mes')
                    ->reactive()
                    ->afterStateUpdated(fn($state, callable $get, callable $set) => self::calcularSueldo($get, $set)),
                Forms\Components\Select::make('anio')
                    ->options(array_combine(range(date('Y'), 2000), range(date('Y'), 2000)))
                    ->required()
                    ->label('Año')
                    ->reactive()
                    ->afterStateUpdated(fn($state, callable $get, callable $set) => self::calcularSueldo($get, $set)),
                Forms\Components\TextInput::make('horas_normales')->required(),
                Forms\Components\TextInput::make('horas_extras')->required(),
                Forms\Components\TextInput::make('pago_horas_normales')
                    ->required()
                    ->numeric()
                    ->reactive()
                    ->afterStateUpdated(fn($state, callable $get, callable $set) => self::calcularSueldo($get, $set)),
                Forms\Components\TextInput::make('pago_horas_extras')
                    ->required()
                    ->numeric()
                    ->reactive()
                    ->afterStateUpdated(fn($state, callable $get, callable $set) => self::calcularSueldo($get, $set)),
                Forms\Components\TextInput::make('total')->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('personal.nombre'), // Cambiar 'personal.id' por 'personal.name'
                TextColumn::make('mes'),
                TextColumn::make('anio'),
                // TextColumn::make('horas_normales'),
                // TextColumn::make('horas_extras'),
                // TextColumn::make('pago_horas_normales'),
                // TextColumn::make('pago_horas_extras'),
                TextColumn::make('total'),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSueldos::route('/'),
            'create' => Pages\CreateSueldo::route('/create'),
            'edit' => Pages\EditSueldo::route('/{record}/edit'),
        ];
    }

    private static function calcularSueldo(callable $get, callable $set)
    {
        $personalId = $get('personal_id');
        $mes = $get('mes');
        $anio = $get('anio');
        $pagoHorasNormales = $get('pago_horas_normales') ?? 0;
        $pagoHorasExtras = $get('pago_horas_extras') ?? 0;

        if ($personalId && $mes && $anio) {
            $inicioMes = "$anio-$mes-01";
            $finMes = date("Y-m-t", strtotime($inicioMes));

            $asistencias = asistencia::where('codigo', $personalId)
                ->whereBetween('fecha', [$inicioMes, $finMes])
                ->get();

            $horas = Sueldo::calcularHoras($asistencias);

            $set('horas_normales', $horas['horas_normales']);
            $set('horas_extras', $horas['horas_extras']);

            $total = ($horas['horas_normales'] * $pagoHorasNormales) + ($horas['horas_extras'] * $pagoHorasExtras);
            $set('total', $total);
        }
    }

    protected static function getEloquentFormState(array $data): array
    {
        $data['total'] = ($data['horas_normales'] * $data['pago_horas_normales']) + ($data['horas_extras'] * $data['pago_horas_extras']);
        return $data;
    }
}
