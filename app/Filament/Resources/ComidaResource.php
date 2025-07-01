<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ComidaResource\Pages;
use App\Filament\Resources\ComidaResource\RelationManagers;
use App\Models\Comida;
use App\Models\personal;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;

class ComidaResource extends Resource
{
    protected static ?string $model = Comida::class;

    protected static ?string $navigationIcon = 'heroicon-o-cake';
    protected static ?string $navigationLabel = 'Control de Comidas';
    protected static ?string $navigationGroup = 'Administrative';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('fecha')
                    ->required()
                    ->default(now()),
                Forms\Components\TimePicker::make('hora')
                    ->required()
                    ->default(now()),
                Forms\Components\Select::make('codigo')
                    ->label('Personal')
                    ->options(personal::all()->pluck('nombre', 'nro_identificacion'))
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('tipo_comida')
                    ->label('Tipo de Comida')
                    ->options([
                        'desayuno' => 'Desayuno',
                        'almuerzo' => 'Almuerzo',
                        'merienda' => 'Merienda',
                        'cena' => 'Cena',
                    ])
                    ->required(),
                Forms\Components\Toggle::make('presente')
                    ->label('Presente')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('fecha')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('hora')
                    ->time()
                    ->sortable(),
                Tables\Columns\TextColumn::make('personal.nombre')
                    ->label('Personal')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tipo_comida')
                    ->label('Tipo de Comida')
                    ->sortable(),
                Tables\Columns\IconColumn::make('presente')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    FilamentExportBulkAction::make('export'),
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
            'index' => Pages\ListComidas::route('/'),
            'create' => Pages\CreateComida::route('/create'),
            'edit' => Pages\EditComida::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
