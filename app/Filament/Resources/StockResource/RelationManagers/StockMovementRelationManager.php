<?php

namespace App\Filament\Resources\StockResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Text;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;

class StockMovementRelationManager extends RelationManager
{
    protected static string $relationship = 'StockMovement';

    public function form(Form $form): Form
    {
        return $form
        ->schema([
            TextInput::make('personal_nombre')
                ->value($this->record->personal->nombre)
                ->label('Nombre de la Persona')
                ->disabled(), // Hace que el campo sea de solo lectura
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
        ->recordTitleAttribute('personal.nombre')
        ->columns([
            Tables\Columns\TextColumn::make('personal.nombre')
                ->label('Nombre de la Persona'),
            Tables\Columns\TextColumn::make('cantidad_movimiento'),
            Tables\Columns\TextColumn::make('fecha_movimiento')
            ->dateTime('d/m/Y')
            ->searchable()
            ->sortable(),
        ])
        ->defaultSort('fecha_movimiento', 'desc')
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
                    FilamentExportBulkAction::make('export')

                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }
}
