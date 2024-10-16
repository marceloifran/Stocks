<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use App\Models\permiso;
use App\Models\personal;
use Filament\Forms\Form;
use Actions\CreateAction;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Wizard;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Wizard\Step;
use Filament\Tables\Columns\Summarizers\Count;
use App\Filament\Resources\PermisoResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Saade\FilamentAutograph\Forms\Components\SignaturePad;
use App\Filament\Resources\PermisoResource\RelationManagers;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Saade\FilamentAutograph\Forms\Components\Enums\DownloadableFormat;

class PermisoResource extends Resource
{
    protected static ?string $model = permiso::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationGroup = 'Administrative';
    protected static ?string $navigationLabel = 'Permissions';



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Step::make('Informacion Personal') 
                    ->schema([
                      TextInput::make('contratista')
                            ->autofocus(),
                          Forms\Components\DateTimePicker::make('fecha_inicio')
                          ->autofocus()
                          ->label('Fecha de Inicio')
                          ->required()
                          ->default(Carbon::now())
                         ,
                         Forms\Components\DateTimePicker::make('fecha_fin')
                         ->autofocus()
                         ->label('Fecha de Fin')
                         ->required()
                         ->default(Carbon::now())
                        ,
                    ])
                    ->icon('heroicon-o-user')
                ])
               ,
                Wizard::make([
                    Step::make('Trabajo') 
                    ->schema([
                     Select::make('tipo_trabajo')
                        ->options([
                            'trabajo_altura' => 'Trabajo de Altura',
                            'trabajo_en_caliente' => 'Trabajo en Caliente',
                            'excavaciones' => 'Excavaciones',
                            'espacios_confinados' => 'Espacios Confinados',
                            'bloqueo_y_etiquetado' => 'Bloqueo y etiquetado',
                        ])
                        ->multiple()
                        ->required(),
                        Checkbox::make('capacitados')
                        ->label('Los trabajadores ejecutantes estan debidamente capacitados y/o entrenados para ejecutar la tarea')
                        ->required(),
                        Select::make('trabajadores')
                        ->options([
                            'activacion_emergencia' => 'Activación de Emergencias',
                            'analisis_trabajo_seguro' => 'Analisis de trabajo seguro',
                            'cianuro' => 'Cianuro 1 y 2',
                            'proteccion_respiratoria' => 'Proteccion respiratoria',
                            'excavaciones' => 'Excavaciones',
                            'trabajo_altura' => 'Trabajo en Altura',
                            'izajes' => 'Izajes',
                            'trabajo_en_caliente' => 'Trabajos en caliente',
                            'espacios_confinados' => 'Espacios confinados',
                            'bloqueo_y_etiquetado' => 'Bloqueo y etiquetado',
                        ])
                        ->multiple(),
                        Forms\Components\Textarea::make('trabajos_a_realizar')
                        ->autofocus()
                        ->label('Trabajos que serán realizados: ')
                        ->nullable()
                        ->autosize(),
                        Forms\Components\Textarea::make('equipos_a_intervenir')
                        ->autofocus()
                        ->label('Lugar/Area/Equipo a intervenir: ')
                        ->nullable()
                        ->autosize(),
                        Select::make('elementos')
                        ->options([
                            'cascos_proteccion' => 'Cascos de protección',
                            'ropa_impermeable' => 'Ropa impermeable',
                            'polainas' => 'Polainas de descame para soldar',
                            'anteojos_seguridad' => 'Anteojos de seguridad',
                            'protector_respiratorio_descartable' => 'Protector respiratorio descartable',
                            'arnes_proteccion' => 'Arnés de protección contra caídas',
                            'antiparras' => 'Antiparras',
                            'protector_respiratorio_filtro' => 'Protector respiratorio con filtros',
                            'dispositivo_salva_caidas' => 'Dispositivo salva caídas',
                            'mascara_respiratoria' => 'Máscara respiratoria con alimentación de aire comprimido',
                            'protector_auditivo' => 'Protector auditivo',
                            'botas_goma' => 'Botas de PVC/Goma con puntera de acero ',
                            'guantes' => 'Guantes',
                            'taburete_alfombra' => 'Taburete/alfombra dieléctrica',
                            'careta_soldador' => 'Careta de soldador',
                            'campera_soldador' => 'Campera de descarne para soldador',
                            'Otros' => 'Otros',
                            'mameluco_descartable' => 'Mameluco descartable',
                            'delantal_soldador' => 'Delantal de descarne para soldador',
                        ])
                        ->multiple(),
                    ])
                    ->icon('heroicon-o-user')
                ])
               ,
               Wizard::make([
                Step::make('Apertura y Cierre Diario') 
                ->schema([
            
                   Forms\Components\DateTimePicker::make('fecha_a_c')
                   ->autofocus()
                   ->label('Fecha')
                   ->nullable()
                   ->default(Carbon::now())
                  ,
                  

                ])
                ->icon('heroicon-o-user')
            ])
           ,
               Wizard::make([
                Step::make('Cierre definitivo del documento PTE') 
                ->schema([
                    Select::make('cierre')
                    ->options([
                        'otras_areas' => 'Otras áreas / equipos informados sobre la finalización del trabajo ',
                        'area_desobstruida' => 'Area desobstruída',
                        'equipos_instalaciones' => 'Equipos / instalaciones inspeccionadas',
                        'area_limpia_organizada' => 'Area limpia y organizada',
                        'protecciones_recolocadas' => 'Protecciones recolocadas',
                        'residuos_procedimientos' => 'Residuos dispuestos de acuerdo con procedimientos',
                        'tarjetas_candados' => 'Tarjetas y candados de bloqueo retirados',
                        'comandos_botoneras' => 'Comandos / botoneras en posición de apagado',
                        'equipos_instalaciones_probadas' => 'Equipos / instalaciones probadas ',
                        'energias_reconectadas' => 'Energías reconectadas',
                        'vallados_señalizacion' => 'Vallados y señalización retirados',
                        'otros_controles' => 'Otros controles',
                    ])
                    ->multiple(),
                   Forms\Components\DateTimePicker::make('fecha_fin_pte')
                   ->autofocus()
                   ->label('Fecha de Fin')
                   ->nullable()
                   ->default(Carbon::now())
                  ,
                  

                ])
                ->icon('heroicon-o-user')
            ])
           ,

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('contratista')
                ->searchable()
                ->label('Contratista')
                ->sortable(),
                Tables\Columns\TextColumn::make('fecha_inicio')
                ->searchable()
                ->label('Fecha de inicio')
                ->sortable(),
                Tables\Columns\TextColumn::make('fecha_fin')
                ->label('Fecha de fin')
                ->searchable()
                ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('Ver Permiso')
                    ->url(fn (permiso $record) => route('personal.exportReporte', $record->id))
                    ->icon('heroicon-o-eye')
                    ,
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    FilamentExportBulkAction::make('export')
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

    public static function getNavigationBadge(): ?string
    {
        return (string) permiso::count();
    }


    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPermisos::route('/'),
            'create' => Pages\CreatePermiso::route('/create'),
            'edit' => Pages\EditPermiso::route('/{record}/edit'),
        ];
    }
}
