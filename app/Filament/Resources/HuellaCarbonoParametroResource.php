<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HuellaCarbonoParametroResource\Pages;
use App\Models\HuellaCarbonoParametro;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Config;

class HuellaCarbonoParametroResource extends Resource
{
    protected static ?string $model = HuellaCarbonoParametro::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static ?string $navigationLabel = 'Parámetros Huella';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('categoria')
                    ->label('Categoría')
                    ->options([
                        'combustible' => 'Combustible',
                        'electricidad' => 'Electricidad',
                        'residuos' => 'Residuos',
                    ])
                    ->required(),

                Forms\Components\TextInput::make('tipo')
                    ->label('Tipo')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('descripcion')
                    ->label('Descripción')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('factor_conversion')
                    ->label('Factor de Conversión')
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->step(0.000001),

                Forms\Components\TextInput::make('unidad_medida')
                    ->label('Unidad de Medida')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('unidad_resultado')
                    ->label('Unidad de Resultado')
                    ->required()
                    ->default('kgCO2e')
                    ->maxLength(255),

                Forms\Components\Toggle::make('activo')
                    ->label('Activo')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('categoria')
                    ->label('Categoría')
                    ->formatStateUsing(function ($state) {
                        $categorias = [
                            'combustible' => 'Combustible',
                            'electricidad' => 'Electricidad',
                            'residuos' => 'Residuos',
                        ];
                        return $categorias[$state] ?? $state;
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('tipo')
                    ->label('Tipo')
                    ->searchable(),

                Tables\Columns\TextColumn::make('descripcion')
                    ->label('Descripción')
                    ->searchable(),

                Tables\Columns\TextColumn::make('factor_conversion')
                    ->label('Factor')
                    ->numeric(6)
                    ->sortable(),

                Tables\Columns\TextColumn::make('unidad_medida')
                    ->label('Unidad')
                    ->searchable(),

                Tables\Columns\IconColumn::make('activo')
                    ->label('Activo')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('categoria')
                    ->label('Categoría')
                    ->options([
                        'combustible' => 'Combustible',
                        'electricidad' => 'Electricidad',
                        'residuos' => 'Residuos',
                    ]),

                Tables\Filters\TernaryFilter::make('activo')
                    ->label('Activo')
                    ->placeholder('Todos')
                    ->trueLabel('Activos')
                    ->falseLabel('Inactivos'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHuellaCarbonoParametros::route('/'),
            'create' => Pages\CreateHuellaCarbonoParametro::route('/create'),
            'edit' => Pages\EditHuellaCarbonoParametro::route('/{record}/edit'),
        ];
    }
}
