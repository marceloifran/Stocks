<?php

namespace App\Filament\Widgets;

use App\Models\StockMovement;
use App\Models\personal;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class FirmasEppWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'Firmas de EPP';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                StockMovement::query()
                    ->with(['personal', 'stock'])
                    ->latest('fecha_movimiento')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('fecha_movimiento')
                    ->date('d/m/Y')
                    ->label('Fecha')
                    ->sortable(),
                Tables\Columns\TextColumn::make('personal.nombre')
                    ->label('Personal')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('personal.departamento')
                    ->label('Departamento')
                    ->searchable(),
                Tables\Columns\TextColumn::make('stock.nombre')
                    ->label('Equipo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tipo')
                    ->label('Tipo')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('firma')
                    ->label('Firma')
                    ->circular(false)
                    ->height(50),
                Tables\Columns\TextColumn::make('created_at')
                    ->since()
                    ->label('Registrado')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('personal_id')
                    ->relationship('personal', 'nombre')
                    ->label('Personal'),
                Tables\Filters\Filter::make('fecha')
                    ->form([
                        Forms\Components\DatePicker::make('desde'),
                        Forms\Components\DatePicker::make('hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['desde'],
                                fn(Builder $query, $date): Builder => $query->whereDate('fecha_movimiento', '>=', $date),
                            )
                            ->when(
                                $data['hasta'],
                                fn(Builder $query, $date): Builder => $query->whereDate('fecha_movimiento', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('ver_firma')
                    ->label('Ver Firma')
                    ->icon('heroicon-o-eye')
                    ->url(fn(StockMovement $record): string => route('firma.ver', ['id' => $record->id]))
                    ->openUrlInNewTab(),
            ]);
    }
}
