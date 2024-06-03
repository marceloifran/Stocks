<?php
namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\CheckboxList;
use App\Filament\Resources\CheckListResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CheckListResource\RelationManagers;
use App\Models\CheckList;
use App\Models\checklists;

class CheckListResource extends Resource
{
    protected static ?string $model = checklists::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('autorizacion')
                    ->autofocus()
                    ->required()
                    ->label('Nombre de quien Autoriza')
                    ->placeholder(__('Autoriza...')),
                Select::make('personal_ids')
                    ->relationship('personal', 'nombre')
                    ->searchable()
                    ->multiple()
                    ->label('Personal')
                    ->required(),
                Forms\Components\DatePicker::make('fecha')
                    ->autofocus()
                    ->required()
                    ->default(Carbon::now()),
                CheckboxList::make('opciones')
                    ->label('Checklist')
                    ->options([
                        'equipo_certificado' => 'Equipo Certificado',
                        'inspeccion_elementos_izaje' => 'Inspeccion de Elementos de Izaje',
                        'estabilizadores' => 'Estabilizadores Extendidos',
                        'condicion_solida_del_sueldo' => 'Condicion Solida del Suelo',
                        'señalero_rigger_visibles' => 'Señalero / Rigger Visibles',
                        'zona_delimitada' => 'Zona Delimitada',
                        'coordinacion' => 'Coordinacion',
                        'ruta_definida' => 'Ruta Definida',
                        'sogueros_en_posicion' => 'Sogueros en Posicion',
                        'permisos_ast_firmados' => 'Permisos y AST firmados',
                    ])
                    ->descriptions([
                        'equipo_certificado' => 'Peso del gancho TN/KG',
                        'inspeccion_elementos_izaje' => 'Peso elementos de izaje TN/KG',
                        'estabilizadores' => 'Peso de carga bruta',
                        'condicion_solida_del_sueldo' => 'CAPACIDAD DE LA GRUA TN/KG CON O SIN BRAZO EXTENDIDO Según corresponda',
                        'señalero_rigger_visibles' => 'Criticidade carga, peso de carga bruta / capacidad bruta x 100',
                        'zona_delimitada' => 'Velocidad del viento',
                        'coordinacion' => 'Radio de Giro',
                        'ruta_definida' => 'Velocidad del viento',
                        'sogueros_en_posicion' => 'Personal Involucrado',
                        'permisos_ast_firmados' => 'Coordinacion con contratistas',
                    ]),
                Forms\Components\TextInput::make('peso_carga_bruta')
                    ->numeric()
                    ->label('Peso de Carga Bruta')
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function (callable $get, callable $set) {
                        $pesoCargaBruta = $get('peso_carga_bruta') ?? 1;
                        $capacidadBruta = $get('capacidad_bruta') ?? 1;
                        $set('criticidad', $pesoCargaBruta / $capacidadBruta * 100);
                    }),
                Forms\Components\TextInput::make('capacidad_bruta')
                    ->numeric()
                    ->label('Capacidad Bruta')
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function (callable $get, callable $set) {
                        $pesoCargaBruta = $get('peso_carga_bruta') ?? 1;
                        $capacidadBruta = $get('capacidad_bruta') ?? 1;
                        $set('criticidad', $pesoCargaBruta / $capacidadBruta * 100);
                    }),
                Forms\Components\TextInput::make('criticidad')
                    ->numeric()
                    ->label('Criticidad de Carga')
                    ->disabled() // Esto es calculado, no se debe editar manualmente
                    ->dehydrated(false), // No incluir este campo al guardar el modelo
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('autorizacion')
                    ->searchable()
                    ->label('Nombre de quien Autoriza'),
                Tables\Columns\TextColumn::make('fecha')
                    ->searchable()
                    ->label('Fecha'),
                Tables\Columns\TextColumn::make('opciones')
                    ->label('Checklist'),
                Tables\Columns\TextColumn::make('personal_ids')
                    ->label('Personal'),
                Tables\Columns\TextColumn::make('peso_carga_bruta')
                    ->label('Peso de Carga Bruta'),
                Tables\Columns\TextColumn::make('capacidad_bruta')
                    ->label('Capacidad Bruta'),
                Tables\Columns\TextColumn::make('criticidad')
                    ->label('Criticidad de Carga'),
            ])
            ->filters([
                // Define filters here
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Define relations here
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCheckLists::route('/'),
            'create' => Pages\CreateCheckList::route('/create'),
            'edit' => Pages\EditCheckList::route('/{record}/edit'),
        ];
    }
}
