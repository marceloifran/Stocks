<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use App\Models\personal;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\IconColumn;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\PersonalResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PersonalResource\RelationManagers;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use App\Filament\Resources\PersonalResource\Widgets\PersonOverview;
use Filament\Forms\Components\DatePicker;

class PersonalResource extends Resource
{
    protected static ?string $model = personal::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Personal';
    protected static ?string $navigationGroup = 'Administrativo';



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                ->autofocus()
                ->unique(ignoreRecord:true)
                ->required()
                ->placeholder(__('Nombre Completo'))->required(),
                Select::make('rol')
                ->options([
                    'Ayudante' => 'Ayudante' ,
                    'Oficial' => 'Oficial' ,
                    'Oficial Especializado' =>   'Oficial Especializado' ,
                    'Medio Oficial' => 'Medi Oficial' ,
                    'Ingeniero/a' => 'Ingenerio/a' ,
                    'HyS' => 'HyS' ,
                    'Topografo' => 'Topografo' ,
                    'Arquitecto/a' => 'Arquitecto/a' ,
                    'Administrativo/a' => 'Administrativo/a' ,
                    'Otros' => 'Otros',
                ])
                ->searchable(),
               DatePicker::make('fecha_entrada')
                ->autofocus()
                ->required()
                ->default(Carbon::now()),
              DatePicker::make('fecha_nacimiento')
                ->autofocus()
                ->required()
                ->default(Carbon::now()),
                Forms\Components\TextInput::make('direccion')
                ->autofocus()
                ->placeholder(__('Direccion')),
                Forms\Components\TextInput::make('telefono')
                ->autofocus()
                ->numeric()
                ->placeholder(__('Telefono')),
                Forms\Components\TextInput::make('dni')
                ->autofocus()
                ->numeric()
                ->placeholder(__('DNI')),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                ->searchable()
                ->sortable(),
                Tables\Columns\TextColumn::make('rol')
                ->searchable()
                ->sortable(),
                 Tables\Columns\TextColumn::make('nro_identificacion')


            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\ViewAction::make(),
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),

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
             RelationManagers\SueldosRelationManager::class,
            RelationManagers\EquiposRelationManager::class,
            RelationManagers\AsistenciaRelationManager::class,
        ];
    }

    public static function getWidgets(): array
    {
        return [
            PersonOverview::class,
        ];
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
