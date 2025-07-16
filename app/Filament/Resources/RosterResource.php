<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RosterResource\Pages;
use App\Filament\Resources\RosterResource\RelationManagers;
use App\Models\Roster;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RosterResource extends Resource
{
    protected static ?string $model = Roster::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Rosters';
    protected static ?string $modelLabel = 'Roster';
    protected static ?string $pluralModelLabel = 'Rosters';
    protected static ?string $navigationGroup = 'Gestión de Obras';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Asignación')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('personal_id')
                                    ->relationship('personal', 'nombre')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->label('Personal'),

                                Forms\Components\Select::make('obra_id')
                                    ->relationship('obra', 'nombre')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->label('Obra'),
                            ]),
                    ]),

                Forms\Components\Section::make('Período de Trabajo')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('fecha_inicio_trabajo')
                                    ->required()
                                    ->label('Inicio Trabajo')
                                    ->reactive()
                                    ->afterStateUpdated(function (callable $set, $state) {
                                        if ($state) {
                                            $fechaInicio = \Carbon\Carbon::parse($state);
                                            $set('fecha_fin_trabajo', $fechaInicio->copy()->addDays(13)->format('Y-m-d'));
                                            $set('fecha_inicio_descanso', $fechaInicio->copy()->addDays(14)->format('Y-m-d'));
                                            $set('fecha_fin_descanso', $fechaInicio->copy()->addDays(27)->format('Y-m-d'));
                                        }
                                    }),

                                Forms\Components\DatePicker::make('fecha_fin_trabajo')
                                    ->required()
                                    ->label('Fin Trabajo'),
                            ]),
                    ]),

                Forms\Components\Section::make('Período de Descanso')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('fecha_inicio_descanso')
                                    ->required()
                                    ->label('Inicio Descanso'),

                                Forms\Components\DatePicker::make('fecha_fin_descanso')
                                    ->required()
                                    ->label('Fin Descanso'),
                            ]),
                    ]),

                Forms\Components\Section::make('Estado y Configuración')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('estado_actual')
                                    ->options([
                                        'trabajando' => 'Trabajando',
                                        'descansando' => 'Descansando',
                                        'finalizado' => 'Finalizado',
                                    ])
                                    ->default('trabajando')
                                    ->required()
                                    ->label('Estado Actual'),

                                Forms\Components\TextInput::make('ciclo_numero')
                                    ->numeric()
                                    ->default(1)
                                    ->required()
                                    ->label('Número de Ciclo'),

                                Forms\Components\Toggle::make('activo')
                                    ->label('Activo')
                                    ->default(true),
                            ]),

                        Forms\Components\Textarea::make('observaciones')
                            ->label('Observaciones')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('personal.nombre')
                    ->label('Personal')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('obra.nombre')
                    ->label('Obra')
                    ->searchable()
                    ->sortable()
                    ->limit(25),

                Tables\Columns\BadgeColumn::make('estado_actual')
                    ->label('Estado')
                    ->colors([
                        'success' => 'trabajando',
                        'warning' => 'descansando',
                        'secondary' => 'finalizado',
                    ]),

                Tables\Columns\TextColumn::make('ciclo_numero')
                    ->label('Ciclo')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('fecha_inicio_trabajo')
                    ->label('Inicio Trabajo')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('fecha_fin_trabajo')
                    ->label('Fin Trabajo')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('fecha_inicio_descanso')
                    ->label('Inicio Descanso')
                    ->date()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('fecha_fin_descanso')
                    ->label('Fin Descanso')
                    ->date()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('dias_restantes')
                    ->label('Días Restantes')
                    ->getStateUsing(function (Roster $record): string {
                        if ($record->estaTrabajando()) {
                            $dias = $record->diasRestantesTrabajo();
                            return $dias > 0 ? "{$dias} días trabajo" : "Último día";
                        } elseif ($record->estaDescansando()) {
                            $dias = $record->diasRestantesDescanso();
                            return $dias > 0 ? "{$dias} días descanso" : "Último día";
                        }
                        return 'Finalizado';
                    })
                    ->badge()
                    ->color(fn(Roster $record): string => match (true) {
                        $record->estaTrabajando() => 'success',
                        $record->estaDescansando() => 'warning',
                        default => 'secondary'
                    }),

                Tables\Columns\IconColumn::make('activo')
                    ->label('Activo')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado_actual')
                    ->options([
                        'trabajando' => 'Trabajando',
                        'descansando' => 'Descansando',
                        'finalizado' => 'Finalizado',
                    ]),

                Tables\Filters\SelectFilter::make('obra_id')
                    ->relationship('obra', 'nombre')
                    ->searchable()
                    ->preload()
                    ->label('Obra'),

                Tables\Filters\TernaryFilter::make('activo')
                    ->label('Activo'),

                Tables\Filters\Filter::make('fecha_trabajo')
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
                                fn(Builder $query, $date): Builder => $query->where('fecha_inicio_trabajo', '>=', $date),
                            )
                            ->when(
                                $data['hasta'],
                                fn(Builder $query, $date): Builder => $query->where('fecha_fin_trabajo', '<=', $date),
                            );
                    })
                    ->label('Período de Trabajo'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('crear_proximo_ciclo')
                    ->label('Próximo Ciclo')
                    ->icon('heroicon-o-arrow-path')
                    ->color('success')
                    ->visible(fn(Roster $record): bool => $record->estado_actual === 'finalizado')
                    ->action(function (Roster $record) {
                        $nuevoCiclo = $record->crearProximoCiclo();

                        // Actualizar el personal
                        $record->personal->update([
                            'estado_roster' => 'trabajando',
                            'proxima_rotacion' => $nuevoCiclo->fecha_fin_trabajo,
                        ]);

                        return redirect()->route('filament.admin.resources.rosters.edit', $nuevoCiclo);
                    })
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListRosters::route('/'),
            'create' => Pages\CreateRoster::route('/create'),
            'edit' => Pages\EditRoster::route('/{record}/edit'),
        ];
    }
}
