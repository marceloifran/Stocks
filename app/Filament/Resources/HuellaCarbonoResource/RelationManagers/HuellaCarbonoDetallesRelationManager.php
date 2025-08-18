<?php

namespace App\Filament\Resources\HuellaCarbonoResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Config;

class HuellaCarbonoDetallesRelationManager extends RelationManager
{
    protected static string $relationship = 'detalles';

    protected static ?string $recordTitleAttribute = 'tipo_fuente';

    protected static ?string $title = 'Fuentes de Emisión';

    public function form(Form $form): Form
    {
        $tiposFuente = Config::get('huella_carbono.tipos_fuente');
        $unidades = Config::get('huella_carbono.unidades');

        $tiposOptions = [];
        $unidadesOptions = [];

        // Preparar opciones para el select de tipo de fuente
        foreach ($tiposFuente as $categoria => $tipos) {
            foreach ($tipos as $key => $value) {
                $tiposOptions[$categoria][$key] = $value;
            }
        }

        // Preparar opciones para el select de unidades
        foreach ($unidades as $categoria => $unidadesCategoria) {
            foreach ($unidadesCategoria as $key => $value) {
                $unidadesOptions[$categoria][$key] = "$key ($value)";
            }
        }

        return $form
            ->schema([
                Forms\Components\Select::make('tipo_fuente')
                    ->label('Tipo de Fuente')
                    ->options($tiposOptions)
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        // Determinar la categoría basada en el tipo seleccionado
                        $tiposFuente = Config::get('huella_carbono.tipos_fuente');
                        $categoria = null;

                        foreach ($tiposFuente as $cat => $tipos) {
                            if (array_key_exists($state, $tipos)) {
                                $categoria = $cat;
                                break;
                            }
                        }

                        // Establecer la unidad predeterminada basada en la categoría
                        if ($categoria === 'combustible') {
                            $set('unidad', 'litros');
                        } elseif ($categoria === 'electricidad') {
                            $set('unidad', 'kilowatt_hora');
                        } elseif ($categoria === 'residuos') {
                            $set('unidad', 'kilogramos');
                        }

                        // Establecer el factor de conversión
                        $factores = Config::get('huella_carbono.factores_conversion');
                        if (isset($factores[$state])) {
                            $set('factor_conversion', $factores[$state]);
                        }
                    }),

                Forms\Components\TextInput::make('cantidad')
                    ->label('Cantidad')
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, $get) {
                        // Calcular emisiones
                        $factor = $get('factor_conversion');
                        $emisiones = $state * $factor;
                        $set('emisiones_co2', round($emisiones, 2));
                    }),

                Forms\Components\Select::make('unidad')
                    ->label('Unidad de Medida')
                    ->options($unidadesOptions)
                    ->required(),

                Forms\Components\TextInput::make('factor_conversion')
                    ->label('Factor de Conversión')
                    ->numeric()
                    ->required()
                    ->default(0)
                    ->minValue(0)
                    ->step(0.000001)
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, $get) {
                        // Recalcular emisiones si cambia el factor
                        $cantidad = $get('cantidad');
                        $emisiones = $cantidad * $state;
                        $set('emisiones_co2', round($emisiones, 2));
                    }),

                Forms\Components\TextInput::make('emisiones_co2')
                    ->label('Emisiones CO2e (kg)')
                    ->numeric()
                    ->disabled()
                    ->default(0),

                Forms\Components\TextInput::make('detalles.identificador_fuente')
                    ->label('Identificador (patente, equipo, etc.)')
                    ->required(),

                Forms\Components\TextInput::make('detalles.horas_operacion')
                    ->label('Horas de operación')
                    ->numeric()
                    ->minValue(0),

                Forms\Components\Textarea::make('detalles.observaciones')
                    ->label('Observaciones')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tipo_fuente')
                    ->label('Tipo de Fuente')
                    ->formatStateUsing(function ($state) {
                        $tiposFuente = Config::get('huella_carbono.tipos_fuente');
                        foreach ($tiposFuente as $categoria => $tipos) {
                            if (isset($tipos[$state])) {
                                return $tipos[$state];
                            }
                        }
                        return $state;
                    }),

                Tables\Columns\TextColumn::make('cantidad')
                    ->label('Cantidad')
                    ->numeric(2),

                Tables\Columns\TextColumn::make('unidad')
                    ->label('Unidad')
                    ->formatStateUsing(function ($state) {
                        $unidades = Config::get('huella_carbono.unidades');
                        foreach ($unidades as $categoria => $unidadesCategoria) {
                            if (isset($unidadesCategoria[$state])) {
                                return $unidadesCategoria[$state];
                            }
                        }
                        return $state;
                    }),

                Tables\Columns\TextColumn::make('emisiones_co2')
                    ->label('Emisiones CO2e')
                    ->numeric(2)
                    ->suffix(' kg'),

                Tables\Columns\TextColumn::make('detalles.identificador_fuente')
                    ->label('Identificador')
                    ->searchable(),

                Tables\Columns\TextColumn::make('detalles.horas_operacion')
                    ->label('Horas')
                    ->numeric()
                    ->formatStateUsing(fn($state) => $state ?? '-'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->after(function ($record) {
                        // Recalcular el total en la huella de carbono
                        $record->huellaCarbono->calcularTotal();
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->after(function ($record) {
                        // Recalcular el total en la huella de carbono
                        $record->huellaCarbono->calcularTotal();
                    }),
                Tables\Actions\DeleteAction::make()
                    ->after(function ($record) {
                        // Recalcular el total en la huella de carbono
                        $record->huellaCarbono->calcularTotal();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->after(function () {
                            // Recalcular el total en la huella de carbono
                            $this->getOwnerRecord()->calcularTotal();
                        }),
                ]),
            ]);
    }
}
