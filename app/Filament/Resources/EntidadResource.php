<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EntidadResource\Pages;
use App\Filament\Resources\EntidadResource\RelationManagers;
use App\Models\entidad;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EntidadResource extends Resource
{
    protected static ?string $model = entidad::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('razon_social')
                    ->autofocus()
                    ->required()
                // ->label(trans('form.name'))
                ->unique(ignoreRecord:true)
                ->placeholder(__('Razon Social'))
                ->required(),
                Forms\Components\TextInput::make('cuit')
                    ->autofocus()
                    ->required(),
                Forms\Components\TextInput::make('direccion')
                    ->autofocus()
                    ->required(),
                Forms\Components\TextInput::make('localidad')
                    ->autofocus()
                    ->required(),
                Forms\Components\TextInput::make('cp')
                    ->autofocus()
                    ->required(),
                Forms\Components\TextInput::make('pcia')
                    ->autofocus()
                    ->required(),
                    FileUpload::make('logo')
                    ->image()
                    ->directory('logos')
                    ->required(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('razon_social')
                ->searchable()
                ->sortable()
                ->icon('heroicon-o-inbox-stack'),
                Tables\Columns\TextColumn::make('cuit')
                ->searchable()
                ->sortable()
                ->icon('heroicon-o-inbox-stack'),
                Tables\Columns\TextColumn::make('direccion')
                ->searchable()
                ->sortable()
                ->icon('heroicon-o-inbox-stack'),
                Tables\Columns\TextColumn::make('localidad')
                ->searchable()
                ->sortable()
                ->icon('heroicon-o-inbox-stack'),
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
            'index' => Pages\ListEntidads::route('/'),
            'create' => Pages\CreateEntidad::route('/create'),
            'edit' => Pages\EditEntidad::route('/{record}/edit'),
        ];
    }
}
