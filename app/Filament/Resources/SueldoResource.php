<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use App\Models\sueldo;
use App\Models\personal;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\SueldoResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SueldoResource\RelationManagers;

class SueldoResource extends Resource
{
    protected static ?string $model = sueldo::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationGroup = 'RRHH';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('personal_id')
                ->options( personal::all()->pluck('nombre', 'id'))
                 ->searchable()
                 ->label('Personal')
                 ->required(),
                 Forms\Components\DatePicker::make('fecha')
                 ->autofocus()
                 ->required()
                 ->default(Carbon::now()),
                 Forms\Components\TextInput::make('monto')
                ->autofocus()
                ->required()
                ->numeric()
                ->default(0)
                ->placeholder(__('Monto'))
                ->required(),
                Select::make('tipo')
                ->options([
                    'Sueldo' => 'Sueldo' ,
                    'Adelanto' => 'Adelanto' ,
                    'Aguinaldo' => 'Aguinaldo' ,
                    'Vacaciones'    => 'Vacaciones' ,

                    'Otros' => 'Otros',
                ])
                ->searchable()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('personal.nombre')
                ->searchable()
                ->sortable()
                ->copyable(),
                Tables\Columns\TextColumn::make('fecha')
                ->searchable()
                ->sortable()
                ->copyable(),
                Tables\Columns\TextColumn::make('monto')
                ->searchable()
                ->sortable()
                ->copyable(),
                Tables\Columns\TextColumn::make('tipo')
                ->searchable()
                ->sortable()
                ->copyable(),
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
            'index' => Pages\ListSueldos::route('/'),
            'create' => Pages\CreateSueldo::route('/create'),
            'edit' => Pages\EditSueldo::route('/{record}/edit'),
        ];
    }
}
