<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HuellaCarbonoResource\Pages;
use App\Filament\Resources\HuellaCarbonoResource\RelationManagers;
use App\Models\HuellaCarbono;
use App\Models\HuellaCarbonoDetalle;
use App\Models\HuellaCarbonoParametro;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\Enums\FontWeight;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class HuellaCarbonoResource extends Resource
{
    protected static ?string $model = HuellaCarbono::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';

    protected static ?string $navigationGroup = 'Gestión Ambiental';

    protected static ?string $navigationLabel = 'Huella de Carbono';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        $tiposFuente = Config::get('huella_carbono.tipos_fuente');
        $categorias = [
            'combustible' => 'Combustible',
            'electricidad' => 'Electricidad',
            'residuos' => 'Residuos',
        ];

        return $form
            ->schema([
                Forms\Components\Section::make('Información General')
                    ->schema([
                        Forms\Components\DatePicker::make('fecha')
                            ->label('Fecha')
                            ->required()
                            ->default(now()),

                        Forms\Components\Textarea::make('notas')
                            ->label('Notas')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Fuente de Emisión')
                    ->schema([
                        Forms\Components\Select::make('categoria_fuente')
                            ->label('Categoría')
                            ->options($categorias)
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn($state, callable $set) => $set('tipo_fuente', null)),

                        Forms\Components\Select::make('tipo_fuente')
                            ->label('Tipo')
                            ->options(function (callable $get) use ($tiposFuente) {
                                $categoria = $get('categoria_fuente');
                                if (!$categoria) {
                                    return [];
                                }

                                return $tiposFuente[$categoria] ?? [];
                            })
                            ->required()
                            ->reactive()
                            ->disabled(fn(callable $get) => !$get('categoria_fuente'))
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                if (!$state) return;

                                $parametro = HuellaCarbonoParametro::where('tipo', $state)
                                    ->where('activo', true)
                                    ->first();

                                if ($parametro) {
                                    $set('factor_conversion', $parametro->factor_conversion);
                                    $set('unidad', $parametro->unidad_medida);
                                }
                            }),

                        Forms\Components\TextInput::make('identificador_fuente')
                            ->label(function (callable $get) {
                                $categoria = $get('categoria_fuente');
                                switch ($categoria) {
                                    case 'combustible':
                                        return 'Patente del vehículo';
                                    case 'electricidad':
                                        return 'Fuente de electricidad';
                                    case 'residuos':
                                        return 'Origen de residuos';
                                    default:
                                        return 'Identificador';
                                }
                            })
                            ->required()
                            ->maxLength(255)
                            ->disabled(fn(callable $get) => !$get('tipo_fuente')),

                        Forms\Components\TextInput::make('horas_operacion')
                            ->label('Horas de operación')
                            ->numeric()
                            ->minValue(0)
                            ->disabled(fn(callable $get) => !$get('tipo_fuente'))
                            ->visible(fn(callable $get) => $get('categoria_fuente') === 'combustible'),

                        Forms\Components\TextInput::make('cantidad')
                            ->label(function (callable $get) {
                                $categoria = $get('categoria_fuente');
                                switch ($categoria) {
                                    case 'combustible':
                                        return 'Litros de combustible';
                                    case 'electricidad':
                                        return 'Kilowatts consumidos';
                                    case 'residuos':
                                        return 'Kilogramos de residuos';
                                    default:
                                        return 'Cantidad';
                                }
                            })
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->disabled(fn(callable $get) => !$get('tipo_fuente'))
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $factor = $get('factor_conversion');
                                if ($state && $factor) {
                                    $emisiones = $state * $factor;
                                    $set('emisiones_co2', round($emisiones, 2));
                                }
                            }),

                        Forms\Components\TextInput::make('factor_conversion')
                            ->label('Factor de conversión')
                            ->helperText('kgCO2e por unidad')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(),

                        Forms\Components\TextInput::make('unidad')
                            ->label('Unidad de medida')
                            ->disabled()
                            ->dehydrated(),

                        Forms\Components\TextInput::make('emisiones_co2')
                            ->label('Emisiones de CO2e (kg)')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(),
                    ])->columns(2),

                Forms\Components\Section::make('Fórmula de cálculo')
                    ->description('La fórmula utilizada para calcular las emisiones es:')
                    ->schema([
                        Forms\Components\Placeholder::make('formula')
                            ->label('')
                            ->content('Emisiones = Consumo de Combustible (Litros) × Factor de Emisión (kg CO2e/Litro)')
                            ->extraAttributes(['class' => 'text-primary-600 font-medium text-lg']),

                        Forms\Components\Placeholder::make('analisis')
                            ->label('Análisis y mitigación')
                            ->content('A partir de estos datos se puede realizar un análisis para determinar medidas de mitigación, como mejorar la eficiencia, identificar equipos que consumen más combustible, o implementar estrategias de compensación como reforestación.')
                    ])->collapsed()
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('fecha')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('detalles.first.tipo_fuente')
                    ->label('Tipo')
                    ->formatStateUsing(function ($state, $record) {
                        try {
                            if (!$record || !$record->detalles || $record->detalles->isEmpty()) {
                                return '-';
                            }

                            $detalle = $record->detalles->first();
                            if (!$detalle) {
                                return '-';
                            }

                            $tiposFuente = Config::get('huella_carbono.tipos_fuente');
                            foreach ($tiposFuente as $categoria => $tipos) {
                                if (isset($tipos[$detalle->tipo_fuente])) {
                                    return $tipos[$detalle->tipo_fuente];
                                }
                            }
                            return $detalle->tipo_fuente ?? '-';
                        } catch (\Exception $e) {
                            return '-';
                        }
                    }),

                Tables\Columns\TextColumn::make('detalles.first.detalles.identificador_fuente')
                    ->label('Identificador')
                    ->formatStateUsing(function ($state, $record) {
                        try {
                            if (!$record || !$record->detalles || $record->detalles->isEmpty()) {
                                return '-';
                            }

                            $detalle = $record->detalles->first();
                            if (!$detalle || !isset($detalle->detalles['identificador_fuente'])) {
                                return '-';
                            }

                            return $detalle->detalles['identificador_fuente'];
                        } catch (\Exception $e) {
                            return '-';
                        }
                    }),

                Tables\Columns\TextColumn::make('detalles.first.detalles.horas_operacion')
                    ->label('Horas de operación')
                    ->formatStateUsing(function ($state, $record) {
                        try {
                            if (
                                !$record ||
                                !$record->detalles ||
                                !$record->detalles->first() ||
                                !isset($record->detalles->first()->detalles['categoria']) ||
                                $record->detalles->first()->detalles['categoria'] !== 'combustible' ||
                                !isset($record->detalles->first()->detalles['horas_operacion'])
                            ) {
                                return '-';
                            }
                            return $record->detalles->first()->detalles['horas_operacion'];
                        } catch (\Exception $e) {
                            return '-';
                        }
                    }),

                Tables\Columns\TextColumn::make('detalles.first.cantidad')
                    ->label('Cantidad')
                    ->formatStateUsing(function ($state, $record) {
                        try {
                            if (!$record || !$record->detalles || $record->detalles->isEmpty()) {
                                return '-';
                            }

                            $detalle = $record->detalles->first();
                            if (!$detalle) {
                                return '-';
                            }

                            return number_format($detalle->cantidad, 2);
                        } catch (\Exception $e) {
                            return '-';
                        }
                    }),

                Tables\Columns\TextColumn::make('total_emisiones')
                    ->label('Emisiones Totales')
                    ->suffix(' kgCO2e')
                    ->numeric(2)
                    ->sortable()
                    ->weight(FontWeight::Bold),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('fecha')
                    ->form([
                        Forms\Components\DatePicker::make('desde')
                            ->label('Desde'),
                        Forms\Components\DatePicker::make('hasta')
                            ->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['desde'],
                                fn(Builder $query, $date): Builder => $query->whereDate('fecha', '>=', $date),
                            )
                            ->when(
                                $data['hasta'],
                                fn(Builder $query, $date): Builder => $query->whereDate('fecha', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                Tables\Actions\Action::make('export_pdf')
                    ->label('Exportar Reporte')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->action(function () {
                        // Obtener datos para el reporte
                        $huellasCarbono = HuellaCarbono::with('detalles')->get();
                        $totalEmisiones = $huellasCarbono->sum('total_emisiones');

                        // Calcular estadísticas por tipo de fuente
                        $estadisticas = [
                            'combustible' => 0,
                            'electricidad' => 0,
                            'residuos' => 0,
                        ];

                        foreach ($huellasCarbono as $huella) {
                            foreach ($huella->detalles as $detalle) {
                                if (isset($detalle->detalles['categoria'])) {
                                    $categoria = $detalle->detalles['categoria'];
                                    if (isset($estadisticas[$categoria])) {
                                        $estadisticas[$categoria] += $detalle->emisiones_co2;
                                    }
                                }
                            }
                        }

                        // Redirigir a la ruta de generación de PDF
                        return redirect()->route('huella-carbono.report', [
                            'totalEmisiones' => $totalEmisiones,
                            'estadisticas' => json_encode($estadisticas),
                        ]);
                    })
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\HuellaCarbonoDetallesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHuellaCarbonos::route('/'),
            'create' => Pages\CreateHuellaCarbono::route('/create'),
            'edit' => Pages\EditHuellaCarbono::route('/{record}/edit'),
        ];
    }
}
