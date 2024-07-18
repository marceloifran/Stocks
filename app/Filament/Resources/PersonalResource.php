<?php

namespace App\Filament\Resources;

use Closure;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use App\Models\personal;
use Filament\Forms\Form;
use Actions\CreateAction;
use Filament\Tables\Table;

use Filament\Actions\Action;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\IconColumn;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\TextEntry;
use App\Filament\Resources\PersonalResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Saade\FilamentAutograph\Forms\Components\SignaturePad;
use App\Filament\Resources\PersonalResource\RelationManagers;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use App\Filament\Resources\PersonalResource\Widgets\CalendarWidget;
use App\Filament\Resources\PersonalResource\Widgets\PersonOverview;
use Saade\FilamentAutograph\Forms\Components\Enums\DownloadableFormat;

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
                ->rules([
                    fn (Get $get): Closure => function ($attribute, $value, $fail) use ($get) {
                        if ($get('nombre')) {
                            $personal = personal::where('nombre', $value)->first();
                            if ($personal && $personal->nombre == $value) {
                                $fail(__('Ya existe una persona con el mismo nombre'));
                            }
                        }
                    },
                ])
                ->unique(ignoreRecord:true)
                ->required()
                ->label('Full Name')
                ->placeholder(__('Full Name'))->required(),
                Select::make('rol')
                ->options([
                    'Ayudante' => 'Ayudante' ,
                    'Oficial' => 'Oficial' ,
                    'Oficial Especializado' =>   'Oficial Especializado' ,
                    'Medio Oficial' => 'Medio Oficial' ,
                    'Ingeniero/a' => 'Ingenerio/a' ,
                    'HyS' => 'HyS' ,
                    'Topografo' => 'Topografo' ,
                    'Arquitecto/a' => 'Arquitecto/a' ,
                    'Administrative/a' => 'Administrative/a' ,
                    'Otros' => 'Otros',
                ])
                ->searchable(),
            //    DatePicker::make('fecha_entrada')
            //     ->autofocus()
            //     ->required()
            //     ->label('Entry Date')
            //     ->default(Carbon::now()),
              DatePicker::make('fecha_nacimiento')
                ->autofocus()
                ->label('Birth Date')
                ->required()
                ->default(Carbon::now()),
                // Forms\Components\TextInput::make('direccion')
                // ->autofocus()
                // ->label('Address')
                // ->placeholder(__('Address')),
                // Forms\Components\TextInput::make('telefono')
                // ->autofocus()
                // ->label('Phone Number')
                // ->numeric()
                // ->placeholder(__('Phone Number')),
                Forms\Components\TextInput::make('nro_identificacion')
                ->autofocus()
                ->rules([
                    fn (Get $get): Closure => function ($attribute, $value, $fail) use ($get) {
                        if ($get('nro_identificacion')) {
                            $personal = personal::where('nro_identificacion', $value)->first();
                            if ($personal && $personal->nro_identificacion == $value) {
                                $fail(__('El identificador ya está en uso, elija otro'));
                            }
                        }
                    },
                ])
                ->numeric()
                ->required()
                ->placeholder(__('Nro de Identificacion')),
                Forms\Components\TextInput::make('dni')
                ->autofocus()
                ->rules([
                    fn (Get $get): Closure => function ($attribute, $value, $fail) use ($get) {
                        if ($get('dni')) {
                            $personal = personal::where('dni', $value)->first();
                            if ($personal && $personal->dni == $value) {
                                $fail(__('El dni ya está en uso, elija otro'));
                            }
                        }
                    },
                ])
                ->numeric()
                ->placeholder(__('DNI')),
                SignaturePad::make('firma')
                ->required()
                ->label('Signature')
                ->downloadableFormats([
                    DownloadableFormat::PNG,
                    DownloadableFormat::JPG,
                    DownloadableFormat::SVG,
                ])
                ->backgroundColor('#FFFFFF')  // Background color on light mode
                ->backgroundColorOnDark('#FFFFFF')     // Background color on dark mode (defaults to backgroundColor)
                ->exportBackgroundColor('#FFFFFF')     // Background color on export (defaults to backgroundColor)
                ->penColor('#040404')                  // Pen color on light mode
                ->penColorOnDark('#040404')            // Pen color on dark mode (defaults to penColor)
                ->exportPenColor('#040404') ,
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
                // Tables\Columns\TextColumn::make('rol')
                // ->searchable()
                // ->sortable(),
                 Tables\Columns\TextColumn::make('nro_identificacion')
                 ->label(trans('tables.identification_number'))
                 ->searchable(),
                //  Tables\Columns\TextColumn::make('dni')
                //  ->searchable()


            ])
            ->defaultSort('nombre', 'asc')
            ->filters([
                
            ])
            ->actions([

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    FilamentExportBulkAction::make('export'),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\StockMoventRelationManager::class,
            RelationManagers\AsistenciaRelationManager::class,
            RelationManagers\PermisoRelationManager::class,
        ];
    }

    //personal, agregar asistencia, osea faltas , sueldo , gasto de sueldos mensual y anual
    // ausencia promedio por persona, contrato medio osea un promedio
    // calendario gral para consultar y agregar eventos


    public static function getWidgets(): array
    {
        return [
            // CalendarWidget::class,
            // PersonOverview::class,
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) personal::count();
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
