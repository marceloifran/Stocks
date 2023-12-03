<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\equipos;
use App\Models\personal;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\EquiposResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\EquiposResource\RelationManagers;
use Filament\Forms\Components\DatePicker;

class EquiposResource extends Resource
{
    protected static ?string $model = equipos::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog';
    protected static ?string $navigationLabel = 'Transport';
    protected static ?string $navigationGroup = 'Transport';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('personal_id')
                ->options( personal::all()->pluck('nombre', 'id'))
                 ->searchable()
                 ->label('Personal encargado')
                 ->required(),
                 DatePicker::make('fecha_ultimo_mantenimiento')
                    ->autofocus()
                    ->label('Fecha ultimo mantenimiento')
                    ->default(now())
                    ->required(),
                Forms\Components\TextInput::make('nombre')
                ->autofocus()
                ->required()
                ->placeholder(__('Nombre')),
                Select::make('estado')
                ->options([
                    'Activo' => 'Activo' ,
                    'Inactivo' => 'Inactivo'
                ])->searchable(),
                Forms\Components\TextInput::make('tipo')
                ->autofocus()
                ->required()
                ->placeholder(__('Tipo')),
                Forms\Components\TextInput::make('patente')
                ->autofocus()
                ->required()
                ->placeholder(__('Patente')),
                FileUpload::make('seguro')->previewable(false)->downloadable()->preserveFilenames()
                ->disk('public')
                ->image()
                ->optimize('webp')
                ->visibility('public'),
                FileUpload::make('rto')->previewable(false)->downloadable()->preserveFilenames()
                ->disk('public')
                ->visibility('public')
                ->optimize('webp')
                ,
                FileUpload::make('poliza')->previewable(false)->downloadable()->preserveFilenames()
                ->disk('public')
                ->visibility('public')
                ->optimize('webp')
               ,

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('personal.nombre')
                ->searchable()
                ->sortable(),
                Tables\Columns\TextColumn::make('fecha_ultimo_mantenimiento')
                ->searchable()
                ->sortable(),
                Tables\Columns\TextColumn::make('nombre')
                ->searchable()
                ->sortable(),
                Tables\Columns\TextColumn::make('tipo')
                ->searchable()
                ->sortable(),
                Tables\Columns\TextColumn::make('estado')
                ->searchable()
                ->sortable(),
                Tables\Columns\TextColumn::make('patente')
                ->searchable()
                ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListEquipos::route('/'),
            'create' => Pages\CreateEquipos::route('/create'),
            'edit' => Pages\EditEquipos::route('/{record}/edit'),
        ];
    }
}
