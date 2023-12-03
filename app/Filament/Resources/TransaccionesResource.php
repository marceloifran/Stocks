<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use App\Models\cuenta;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\transacciones;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\TransaccionesResource\Pages;
use App\Filament\Resources\TransaccionesResource\RelationManagers;

class TransaccionesResource extends Resource
{
    protected static ?string $model = transacciones::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationLabel = 'Transactions';
    protected static ?string $navigationGroup = 'Finance';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                    Select::make('cuenta_id')
                  ->options( cuenta::all()->pluck('nombre', 'id'))
                    ->required()
                    ->label('Cuenta')
                    ->searchable()
                    ->required(),
                    Forms\Components\TextInput::make('monto')
                    ->autofocus()
                    ->required()
                    ->numeric()
                    ->placeholder(__('Monto'))
                    ->required()
                   ,
                   Select::make('tipo')
                   ->options([
                       'Ingreso' => 'Ingreso' ,
                       'Gasto' => 'Gasto' ,
                   ])
                   ->searchable()
                   ->required(),
                    Forms\Components\DatePicker::make('fecha')
                    ->autofocus()
                    ->required()
                    ->default(Carbon::now())
                   ,
                    Forms\Components\Textarea::make('descripcion')
                    ->autofocus()
                    ->placeholder(__('Descripcion'))
                    ->nullable(),

                ]);



    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('cuenta.nombre')
                ->searchable()
                ->sortable(),
                Tables\Columns\TextColumn::make('fecha')
                ->searchable()
                ->sortable(),
                Tables\Columns\TextColumn::make('monto')
                ->searchable()
                ->sortable(),
                Tables\Columns\TextColumn::make('tipo')
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
            'index' => Pages\ListTransacciones::route('/'),
            'create' => Pages\CreateTransacciones::route('/create'),
            'edit' => Pages\EditTransacciones::route('/{record}/edit'),
        ];
    }
}
