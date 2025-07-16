<?php

namespace App\Filament\Resources;

use Closure;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use App\Models\personal;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\IconColumn;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\TextEntry;
use App\Filament\Resources\PersonalResource\Pages;
use Saade\FilamentAutograph\Forms\Components\SignaturePad;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use App\Filament\Resources\PersonalResource\RelationManagers;
use Saade\FilamentAutograph\Forms\Components\Enums\DownloadableFormat;
use App\Filament\Resources\PersonalResource\Widgets\PersonOverview;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Tabs;

class PersonalResource extends Resource
{
    protected static ?string $model = personal::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Personal';
    protected static ?string $navigationGroup = 'Administrative';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->autofocus()
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->label(trans('form.name'))
                    ->placeholder(__(trans('form.name'))),
                Forms\Components\TextInput::make('nro_identificacion')
                    ->autofocus()
                    ->label(trans('form.identification'))
                    // ->rules([
                    //     fn(Get $get): Closure => function ($attribute, $value, $fail) use ($get) {
                    //         if ($get('nro_identificacion') && $value != Personal::find($get('id'))->nro_identificacion) {
                    //             $personal = Personal::where('nro_identificacion', $value)->first();
                    //             if ($personal && $personal->nro_identificacion == $value) {
                    //                 $fail(__('El identificador ya está en uso, elija otro'));
                    //             }
                    //         }
                    //     },
                    // ])
                    ->numeric()
                    ->required()
                    ->placeholder(__(trans('form.identification'))),
                Forms\Components\TextInput::make('dni')
                    ->unique(ignoreRecord: true)
                    ->autofocus()
                    ->numeric()
                    ->placeholder(__('DNI')),
                Forms\Components\Select::make('departamento')
                    ->options([
                        'Administración' => 'Administración',
                        'Producción' => 'Producción',
                        'Logística' => 'Logística',
                        'Ventas' => 'Ventas',
                        'Recursos Humanos' => 'Recursos Humanos',
                        'TI' => 'TI',
                        'Otro' => 'Otro',
                    ])
                    ->searchable()
                    ->placeholder('Seleccione un departamento'),

                Forms\Components\Section::make('Gestión de Obras y Rosters')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('obra_actual_id')
                                    ->relationship('obraActual', 'nombre')
                                    ->searchable()
                                    ->preload()
                                    ->label('Obra Actual')
                                    ->placeholder('Seleccione una obra'),

                                Forms\Components\Select::make('tipo_roster')
                                    ->options([
                                        '14x14' => '14 días trabajo / 14 días descanso',
                                        '21x7' => '21 días trabajo / 7 días descanso',
                                        '28x14' => '28 días trabajo / 14 días descanso',
                                        'fijo' => 'Horario fijo (sin rotación)',
                                    ])
                                    ->default('14x14')
                                    ->label('Tipo de Roster'),
                            ]),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('estado_roster')
                                    ->options([
                                        'trabajando' => 'Trabajando',
                                        'descansando' => 'Descansando',
                                        'inactivo' => 'Inactivo',
                                    ])
                                    ->default('inactivo')
                                    ->label('Estado del Roster'),

                                Forms\Components\DatePicker::make('fecha_inicio_roster')
                                    ->label('Inicio del Roster'),

                                Forms\Components\DatePicker::make('proxima_rotacion')
                                    ->label('Próxima Rotación'),
                            ]),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('dias_trabajados_consecutivos')
                                    ->numeric()
                                    ->default(0)
                                    ->label('Días Trabajados Consecutivos'),

                                Forms\Components\TextInput::make('dias_descanso_consecutivos')
                                    ->numeric()
                                    ->default(0)
                                    ->label('Días Descanso Consecutivos'),

                                Forms\Components\Toggle::make('disponible_para_asignacion')
                                    ->label('Disponible para Asignación')
                                    ->default(true),
                            ]),

                        Forms\Components\Textarea::make('observaciones_roster')
                            ->label('Observaciones del Roster')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),
                // SignaturePad::make('firma')
                //     ->required()
                //     ->label('Signature')
                //     ->downloadableFormats([
                //         DownloadableFormat::PNG,
                //         DownloadableFormat::JPG,
                //         DownloadableFormat::SVG,
                //     ])
                //     ->backgroundColor('#FFFFFF')
                //     ->backgroundColorOnDark('#FFFFFF')
                //     ->exportBackgroundColor('#FFFFFF')
                //     ->penColor('#040404')
                //     ->penColorOnDark('#040404')
                //     ->exportPenColor('#040404'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable()
                    ->icon('heroicon-o-user')
                    ->label(trans('tables.name'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('nro_identificacion')
                    ->label(trans('tables.identification_number'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('dni')
                    ->searchable(),
                Tables\Columns\TextColumn::make('departamento')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Administración' => 'info',
                        'Producción' => 'success',
                        'Logística' => 'warning',
                        'Ventas' => 'danger',
                        'Recursos Humanos' => 'primary',
                        'TI' => 'gray',
                        default => 'secondary',
                    }),
                Tables\Columns\TextColumn::make('obraActual.nombre')
                    ->label('Obra Actual')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary')
                    ->default('Sin asignar'),

                Tables\Columns\BadgeColumn::make('estado_roster')
                    ->label('Estado Roster')
                    ->colors([
                        'success' => 'trabajando',
                        'warning' => 'descansando',
                        'secondary' => 'inactivo',
                    ]),

                Tables\Columns\TextColumn::make('tipo_roster')
                    ->label('Tipo Roster')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('proxima_rotacion')
                    ->label('Próxima Rotación')
                    ->date()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('disponible_para_asignacion')
                    ->label('Disponible')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->toggleable(),

                Tables\Columns\IconColumn::make('presente')
                    ->label('Presente Hoy')
                    ->boolean()
                    ->getStateUsing(fn(personal $record): bool => $record->presente())
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
            ])
            ->defaultSort('nombre', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('obra_actual_id')
                    ->relationship('obraActual', 'nombre')
                    ->searchable()
                    ->preload()
                    ->label('Obra Actual'),

                Tables\Filters\SelectFilter::make('estado_roster')
                    ->options([
                        'trabajando' => 'Trabajando',
                        'descansando' => 'Descansando',
                        'inactivo' => 'Inactivo',
                    ])
                    ->label('Estado del Roster'),

                Tables\Filters\SelectFilter::make('tipo_roster')
                    ->options([
                        '14x14' => '14x14',
                        '21x7' => '21x7',
                        '28x14' => '28x14',
                        'fijo' => 'Fijo',
                    ])
                    ->label('Tipo de Roster'),

                Tables\Filters\TernaryFilter::make('disponible_para_asignacion')
                    ->label('Disponible para Asignación'),

                Tables\Filters\Filter::make('necesita_rotacion')
                    ->query(fn(Builder $query): Builder => $query->whereNotNull('proxima_rotacion')->whereDate('proxima_rotacion', '<=', now()))
                    ->label('Necesita Rotación'),

                Tables\Filters\SelectFilter::make('departamento')
                    ->options([
                        'Administración' => 'Administración',
                        'Producción' => 'Producción',
                        'Logística' => 'Logística',
                        'Ventas' => 'Ventas',
                        'Recursos Humanos' => 'Recursos Humanos',
                        'TI' => 'TI',
                        'Otro' => 'Otro',
                    ])
                    ->label('Departamento'),
            ])
            ->actions([
                Tables\Actions\Action::make('pdf_credencial')
                    ->label('')
                    ->tooltip('Descargar PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('danger')
                    ->url(fn(personal $record): string => route('personal.credencial.pdf', ['id' => $record->id]))
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('ver_credencial')
                    ->label('')
                    ->tooltip('Ver Credencial')
                    ->icon('heroicon-o-identification')
                    ->color('info')
                    ->url(fn(personal $record): string => route('personal.credencial.ver', ['id' => $record->id]))
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('ver_asistencias')
                    ->label('')
                    ->tooltip('Ver asistencias')
                    ->icon('heroicon-o-calendar')
                    ->color('success')
                    ->url(fn(personal $record): string => route('asistencia.personal', ['record' => $record->id]))
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('ver_comidas')
                    ->label('')
                    ->tooltip('Ver comidas')
                    ->icon('heroicon-o-cake')
                    ->color('warning')
                    ->url(fn(personal $record): string => route('comida.personal', ['record' => $record->id]))
                    ->openUrlInNewTab(),

                Tables\Actions\Action::make('asignar_obra')
                    ->label('')
                    ->tooltip('Asignar a Obra')
                    ->icon('heroicon-o-building-office-2')
                    ->color('primary')
                    ->visible(fn(personal $record): bool => $record->estaDisponible())
                    ->form([
                        Forms\Components\Select::make('obra_id')
                            ->relationship('obra', 'nombre', fn($query) => $query->where('activa', true))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Seleccionar Obra'),

                        Forms\Components\DatePicker::make('fecha_inicio')
                            ->default(now())
                            ->required()
                            ->label('Fecha de Inicio'),
                    ])
                    ->action(function (personal $record, array $data): void {
                        $obra = \App\Models\Obra::find($data['obra_id']);
                        $fechaInicio = \Carbon\Carbon::parse($data['fecha_inicio']);

                        $record->asignarAObra($obra, $fechaInicio);

                        \Filament\Notifications\Notification::make()
                            ->title('Personal asignado exitosamente')
                            ->body("El personal {$record->nombre} ha sido asignado a la obra {$obra->nombre}")
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation(),

                Tables\Actions\Action::make('cambiar_estado_roster')
                    ->label('')
                    ->tooltip('Cambiar Estado Roster')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->visible(fn(personal $record): bool => $record->obra_actual_id !== null)
                    ->action(function (personal $record): void {
                        if ($record->estaTrabajando()) {
                            $record->iniciarDescanso();
                            $mensaje = 'Personal cambiado a período de descanso';
                        } else {
                            $record->update(['estado_roster' => 'trabajando']);
                            $mensaje = 'Personal cambiado a período de trabajo';
                        }

                        \Filament\Notifications\Notification::make()
                            ->title('Estado actualizado')
                            ->body($mensaje)
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation(),

                Tables\Actions\Action::make('finalizar_asignacion')
                    ->label('')
                    ->tooltip('Finalizar Asignación')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn(personal $record): bool => $record->obra_actual_id !== null)
                    ->action(function (personal $record): void {
                        $obraNombre = $record->obraActual?->nombre ?? 'obra';
                        $record->finalizarAsignacion();

                        \Filament\Notifications\Notification::make()
                            ->title('Asignación finalizada')
                            ->body("El personal {$record->nombre} ha sido desasignado de {$obraNombre}")
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation(),

                Tables\Actions\Action::make('ver_roster')
                    ->label('')
                    ->tooltip('Ver Roster')
                    ->icon('heroicon-o-calendar-days')
                    ->color('info')
                    ->visible(fn(personal $record): bool => $record->obra_actual_id !== null)
                    ->url(fn(personal $record): string => route('filament.admin.resources.rosters.index', ['tableFilters[personal_id][value]' => $record->id]))
                    ->openUrlInNewTab(),

                Tables\Actions\EditAction::make()
                    ->label('')
                    ->tooltip('Editar')
                    ->color('gray'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    FilamentExportBulkAction::make('export'),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->persistFiltersInSession()
            ->filtersTriggerAction(
                fn(Tables\Actions\Action $action) => $action
                    ->button()
                    ->label('Filtros')
            )
            ->filtersLayout(FiltersLayout::AboveContent)
            ->persistSortInSession()
            ->persistSearchInSession()
            ->persistColumnSearchesInSession();
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\StockMoventRelationManager::class,
            RelationManagers\AsistenciaRelationManager::class,
            RelationManagers\ComidasRelationManager::class,
        ];
    }

    public static function getWidgets(): array
    {
        return [];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) Personal::count();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPersonals::route('/'),
            'create' => Pages\CreatePersonal::route('/create'),
            'edit' => Pages\EditPersonal::route('/{record}/edit'),
        ];
    }
}
