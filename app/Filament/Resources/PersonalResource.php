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

                Forms\Components\Section::make('Gestión de Obras')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('obra_actual_id')
                                    ->relationship('obraActual', 'nombre')
                                    ->searchable()
                                    ->preload()
                                    ->label('Obra Actual')
                                    ->placeholder('Seleccione una obra'),

                                Forms\Components\Toggle::make('disponible_para_asignacion')
                                    ->label('Disponible para Asignación')
                                    ->default(true),
                            ]),

                        Forms\Components\Textarea::make('observaciones_obra')
                            ->label('Observaciones de la Obra')
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
            ->filters([])
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
