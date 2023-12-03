<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Cuenta;
use Filament\Forms\Form;
use App\Tables\Columns\IconColorColumn;

use Filament\Tables\Table;
use Guava\FilamentIconPicker\Forms\IconPicker;

use Filament\Resources\Resource;
use function Laravel\Prompts\select;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Checkbox;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\ColorPicker;
use Filament\Tables\Columns\Summarizers\Sum;
use App\Filament\Resources\CuentaResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use App\Filament\Resources\CuentaResource\RelationManagers;
use App\Filament\Resources\CuentaResource\RelationManagers\TransaccionesRelationManager;

class CuentaResource extends Resource
{
    protected static ?string $model = Cuenta::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Accounts';
    protected static ?string $navigationGroup = 'Finance';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\TextInput::make('codigo')
            ->autofocus()
            ->required()
            ->unique(ignoreRecord:true)
            ->placeholder(__('Codigo'))
            ->required()
           ,
           Forms\Components\TextInput::make('nombre')
           ->autofocus()
           ->required()
           ->placeholder(__('Nombre de la Cuenta'))
           ->required(),
           Forms\Components\Textarea::make('descripcion')
           ->autofocus()
           ->required()
           ->placeholder(__('Descripcion de la Cuenta'))
           ->required(),
           Forms\Components\TextInput::make('saldo')
           ->autofocus()
           ->required()
           ->placeholder(__('Saldo de la Cuenta'))
           ->required(),
           Select::make('tipo')
           ->options([
               'Activo' => 'Activo' ,
               'Pasivo' => 'Pasivo' ,
           ])
           ->label('Tipo de Cuenta')
           ->searchable(),
         select::make('activo')
         ->options([
            true => 'Activa',
            false => 'Inactiva'
         ])
         ->label('Estado de la Cuenta')
         ->searchable()
         ->required()

        ]);
}

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('codigo')
                ->searchable()
                ->sortable(),
                Tables\Columns\TextColumn::make('codigo')
                ->searchable()
                ->sortable(),
                Tables\Columns\TextColumn::make('nombre')
                ->searchable()
                ->sortable(),
                Tables\Columns\TextColumn::make('saldo')
                ->searchable()
                ->sortable()
                ->summarize([
                    Sum::make()
                    ->money()
                    ->label('Total')
                ])
                ->label('Total'),
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
            TransaccionesRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCuentas::route('/'),
            'create' => Pages\CreateCuenta::route('/create'),
            'edit' => Pages\EditCuenta::route('/{record}/edit'),
        ];
    }
}
