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
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\Summarizers\Count;
use App\Filament\Resources\PermisoResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Saade\FilamentAutograph\Forms\Components\SignaturePad;
use App\Filament\Resources\PermisoResource\RelationManagers;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
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
                Select::make('personal_ids')
               ->relationship('personal', 'nombre')
                ->searchable()
                ->multiple()
                ->label('Personal')
                ->required(),
                  Forms\Components\DatePicker::make('fecha')
                  ->autofocus()
                  ->required()
                  ->default(Carbon::now())
                 ,
                 Forms\Components\Textarea::make('tipo')
                 ->autofocus()
                 ->placeholder(__('Actividad'))
                 ->label('Actividad')
                 ->nullable(),
                  Forms\Components\Textarea::make('descripcion')
                  ->autofocus()
                  ->placeholder(__('Sector'))
                  ->label('Sector')
                  ->nullable(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tipo')
                ->searchable()
                ->label('Actividad')
                ->sortable(),
                Tables\Columns\TextColumn::make('descripcion')
                ->searchable()
                ->label('Sector')
                ->sortable(),
                Tables\Columns\TextColumn::make('fecha')
                ->searchable()
                ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([

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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPermisos::route('/'),
            'create' => Pages\CreatePermiso::route('/create'),
            'edit' => Pages\EditPermiso::route('/{record}/edit'),
        ];
    }
}
