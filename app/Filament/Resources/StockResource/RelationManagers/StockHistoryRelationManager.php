<?php

namespace App\Filament\Resources\StockResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StockHistoryRelationManager extends RelationManager
{
    protected static string $relationship = 'StockHistory';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('valor_nuevo')
                    ->required()
                    ->maxLength(255)
                    ,
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('valor_nuevo')
            ->columns([
                Tables\Columns\TextColumn::make('valor_anterior'),
                Tables\Columns\TextColumn::make('valor_nuevo'),
                Tables\Columns\TextColumn::make('fecha_nueva')
                ->dateTime('d/m/Y')
                ->searchable()
                ->sortable(),
            ])
            ->filters([
                Filter::make('created_at')
    ->form([
        Forms\Components\DatePicker::make('Desde'),
        Forms\Components\DatePicker::make('Hasta'),
    ])
    ->query(function (Builder $query, array $data): Builder {
        return $query
            ->when(
                $data['Desde'],
                fn (Builder $query, $date): Builder => $query->whereDate('fecha_nueva', '>=', $date),
            )
            ->when(
                $data['Hasta'],
                fn (Builder $query, $date): Builder => $query->whereDate('fecha_nueva', '<=', $date),
            );
    })
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
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
