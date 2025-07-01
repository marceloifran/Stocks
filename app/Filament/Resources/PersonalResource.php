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
                    ->label('Departamento')
                    ->multiple(),
                Tables\Filters\Filter::make('presente')
                    ->label('Presentes Hoy')
                    ->query(fn(Builder $query): Builder => $query->whereHas('asistencia', function ($query) {
                        $query->whereDate('fecha', now()->toDateString())->where('estado', 'entrada');
                    }))
            ])
            ->actions([
                Tables\Actions\Action::make('ver_credencial')
                    ->label('ID')
                    ->tooltip('Ver credencial')
                    ->icon('heroicon-o-identification')
                    ->color('primary')
                    ->url(fn(personal $record): string => route('personal.credencial.ver', ['id' => $record->id]))
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('pdf_credencial')
                    ->label('')
                    ->tooltip('Descargar PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('danger')
                    ->url(fn(personal $record): string => route('personal.credencial.pdf', ['id' => $record->id]))
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
            ->groups([
                Tables\Grouping\Group::make('departamento')
                    ->label('Departamento')
                    ->collapsible(),
            ]);
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
