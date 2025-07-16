<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ObraResource\Pages;
use App\Filament\Resources\ObraResource\RelationManagers;
use App\Models\Obra;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ObraResource extends Resource
{
    protected static ?string $model = Obra::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationLabel = 'Obras';
    protected static ?string $modelLabel = 'Obra';
    protected static ?string $pluralModelLabel = 'Obras';
    protected static ?string $navigationGroup = 'Gestión de Obras';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información General')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('nombre')
                                    ->required()
                                    ->maxLength(255)
                                    ->label('Nombre de la Obra'),

                                Forms\Components\TextInput::make('codigo')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255)
                                    ->label('Código')
                                    ->helperText('Código único para identificar la obra'),
                            ]),

                        Forms\Components\Textarea::make('descripcion')
                            ->label('Descripción')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Detalles del Proyecto')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('cliente')
                                    ->label('Cliente')
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('ubicacion')
                                    ->label('Ubicación')
                                    ->maxLength(255),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('fecha_inicio')
                                    ->label('Fecha de Inicio'),

                                Forms\Components\DatePicker::make('fecha_fin_estimada')
                                    ->label('Fecha Fin Estimada'),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('estado')
                                    ->options([
                                        'planificada' => 'Planificada',
                                        'en_progreso' => 'En Progreso',
                                        'pausada' => 'Pausada',
                                        'completada' => 'Completada',
                                        'cancelada' => 'Cancelada',
                                    ])
                                    ->default('planificada')
                                    ->required()
                                    ->label('Estado'),

                                Forms\Components\TextInput::make('presupuesto')
                                    ->numeric()
                                    ->prefix('$')
                                    ->label('Presupuesto'),
                            ]),

                        Forms\Components\Toggle::make('activa')
                            ->label('Obra Activa')
                            ->default(true),
                    ]),

                Forms\Components\Section::make('Contactos')
                    ->schema([
                        Forms\Components\Repeater::make('contactos')
                            ->schema([
                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        Forms\Components\TextInput::make('nombre')
                                            ->label('Nombre')
                                            ->required(),

                                        Forms\Components\TextInput::make('telefono')
                                            ->label('Teléfono')
                                            ->tel(),

                                        Forms\Components\TextInput::make('email')
                                            ->label('Email')
                                            ->email(),
                                    ]),

                                Forms\Components\TextInput::make('cargo')
                                    ->label('Cargo/Posición'),
                            ])
                            ->label('Contactos de la Obra')
                            ->collapsible()
                            ->itemLabel(fn(array $state): ?string => $state['nombre'] ?? null),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('codigo')
                    ->label('Código')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('nombre')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('cliente')
                    ->label('Cliente')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('ubicacion')
                    ->label('Ubicación')
                    ->searchable()
                    ->toggleable()
                    ->limit(25),

                Tables\Columns\BadgeColumn::make('estado')
                    ->label('Estado')
                    ->colors([
                        'secondary' => 'planificada',
                        'primary' => 'en_progreso',
                        'warning' => 'pausada',
                        'success' => 'completada',
                        'danger' => 'cancelada',
                    ]),

                Tables\Columns\TextColumn::make('personal_actual_count')
                    ->label('Personal Asignado')
                    ->counts('personalActual')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('personal_trabajando_count')
                    ->label('Trabajando')
                    ->getStateUsing(fn(Obra $record): int => $record->personalTrabajando()->count())
                    ->badge()
                    ->color('success'),

                Tables\Columns\IconColumn::make('activa')
                    ->label('Activa')
                    ->boolean()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('fecha_inicio')
                    ->label('Inicio')
                    ->date()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->options([
                        'planificada' => 'Planificada',
                        'en_progreso' => 'En Progreso',
                        'pausada' => 'Pausada',
                        'completada' => 'Completada',
                        'cancelada' => 'Cancelada',
                    ]),

                Tables\Filters\TernaryFilter::make('activa')
                    ->label('Obra Activa'),

                Tables\Filters\Filter::make('con_personal')
                    ->query(fn(Builder $query): Builder => $query->has('personalActual'))
                    ->label('Con Personal Asignado'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('ver_personal')
                    ->label('Ver Personal')
                    ->icon('heroicon-o-users')
                    ->url(fn(Obra $record): string => route('filament.admin.resources.personals.index', ['tableFilters[obra_actual_id][value]' => $record->id]))
                    ->openUrlInNewTab(),
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
            'index' => Pages\ListObras::route('/'),
            'create' => Pages\CreateObra::route('/create'),
            'edit' => Pages\EditObra::route('/{record}/edit'),
        ];
    }
}
