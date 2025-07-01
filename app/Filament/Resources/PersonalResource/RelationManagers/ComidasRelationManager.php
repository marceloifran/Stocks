<?php

namespace App\Filament\Resources\PersonalResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;

class ComidasRelationManager extends RelationManager
{
    protected static string $relationship = 'comidas';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('fecha')
                    ->required(),
                Forms\Components\TimePicker::make('hora')
                    ->required(),
                Forms\Components\Select::make('tipo_comida')
                    ->options([
                        'desayuno' => 'Desayuno',
                        'almuerzo' => 'Almuerzo',
                        'merienda' => 'Merienda',
                        'cena' => 'Cena',
                    ])
                    ->required(),
                Forms\Components\Toggle::make('presente')
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('codigo')
            ->columns([
                Tables\Columns\TextColumn::make('fecha')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('hora')
                    ->time()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tipo_comida')
                    ->sortable(),
                Tables\Columns\IconColumn::make('presente')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tipo_comida')
                    ->options([
                        'desayuno' => 'Desayuno',
                        'almuerzo' => 'Almuerzo',
                        'merienda' => 'Merienda',
                        'cena' => 'Cena',
                    ]),
                Tables\Filters\Filter::make('fecha')
                    ->form([
                        Forms\Components\DatePicker::make('desde'),
                        Forms\Components\DatePicker::make('hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['desde'],
                                fn(Builder $query, $date): Builder => $query->whereDate('fecha', '>=', $date),
                            )
                            ->when(
                                $data['hasta'],
                                fn(Builder $query, $date): Builder => $query->whereDate('fecha', '<=', $date),
                            );
                    }),
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
                    FilamentExportBulkAction::make('export'),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }
}
