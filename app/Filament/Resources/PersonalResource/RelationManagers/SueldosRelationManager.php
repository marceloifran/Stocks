<?php

namespace App\Filament\Resources\PersonalResource\RelationManagers;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use App\Models\personal;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class SueldosRelationManager extends RelationManager
{
    protected static string $relationship = 'sueldo';

    public function form(Form $form): Form
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

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('tipo')
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
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
}
