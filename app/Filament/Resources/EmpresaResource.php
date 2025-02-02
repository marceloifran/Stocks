<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmpresaResource\Pages;
use App\Filament\Resources\EmpresaResource\RelationManagers;
use App\Models\Empresa;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmpresaResource extends Resource
{
    protected static ?string $model = empresa::class;

    protected static ?string $navigationGroup = 'Administrative';

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

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
                Forms\Components\TextInput::make('cuit')
                    ->autofocus()
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->label(trans('form.cuit'))
                    ->placeholder(__(trans('form.cuit'))),
                Forms\Components\TextInput::make('direccion')
                    ->autofocus()
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->label(trans('form.direction'))
                    ->placeholder(__(trans('form.direction'))),
                Forms\Components\TextInput::make('localidad')
                    ->autofocus()
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->label(trans('form.location'))
                    ->placeholder(__(trans('form.location'))),
                Forms\Components\TextInput::make('cp')
                    ->autofocus()
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->label(trans('form.cp'))
                    ->placeholder(__(trans('form.cp'))),
                Forms\Components\TextInput::make('pcia')
                    ->autofocus()
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->label(trans('form.pcia'))
                    ->placeholder(__(trans('form.pcia'))),
                Forms\Components\TextInput::make('razon_social')
                    ->autofocus()
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->label(trans('form.razon'))
                    ->placeholder(__(trans('form.razon'))),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable()
                    ->icon('heroicon-o-user')
                    ->label(trans('tables.name')),

                Tables\Columns\TextColumn::make('direccion')
                    ->searchable()
                    ->icon('heroicon-o-map-pin')
                    ->label(trans('tables.direction')),
                Tables\Columns\TextColumn::make('cp')
                    ->searchable()
                    ->icon('heroicon-o-user')
                    ->label(trans('tables.cp')),

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

    public static function validarCuit($cuit)
    {
        $cuit = str_replace(['-', ' '], '', $cuit); // Eliminar guiones
        if (strlen($cuit) !== 11 || !ctype_digit($cuit)) return false;

        $coeficientes = [5, 4, 3, 2, 7, 6, 5, 4, 3, 2];
        $suma = 0;

        for ($i = 0; $i < 10; $i++) {
            $suma += $cuit[$i] * $coeficientes[$i];
        }

        $verificador = 11 - ($suma % 11);
        $verificador = ($verificador == 11) ? 0 : (($verificador == 10) ? 9 : $verificador);

        return $verificador == $cuit[10];
    }


    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) Empresa::count();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmpresas::route('/'),
            'create' => Pages\CreateEmpresa::route('/create'),
            'edit' => Pages\EditEmpresa::route('/{record}/edit'),
        ];
    }
}
