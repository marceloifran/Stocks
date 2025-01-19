<?php

namespace App\Filament\Resources;


use Filament\Forms;
use App\Models\obra;
use Filament\Tables;
use App\Models\personal;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ObraResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ObraResource\RelationManagers;

class ObraResource extends Resource
{
    protected static ?string $model =obra::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationGroup = 'Administrative';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->label('Nombre')
                    ->required(),
                Forms\Components\TextInput::make('estado')
                    ->label('Estado')
                    ->required(),
                Forms\Components\TextInput::make('presupuesto')
                    ->label('Presupuesto')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')
                    ->label('Nombre')
                    ->searchable()
                    ,
               TextColumn::make('estado')
                    ->label('Estado')
                    ->searchable()
                    ,
                    TextColumn::make('personal_count')
                    ->label('Personal en Obra')
                    ->icon('heroicon-o-users')
                    ->counts('personal') // Relación definida en el modelo `obra`
                    
                    ->badge(function ($record) {
                        return $record->personal_count;
                    })
                    ->color(function ($record) {
                        return $record->personal_count > 10 ? 'success' : 'danger';
                    }),
                    TextColumn::make('fecha_arranque')
                    ->label('Fecha de Arranque')
                    ->searchable()
                ->icon('heroicon-o-calendar-days')
                    ,
                    TextColumn::make('fecha_final')
                    ->label('Fecha Final')
                    ->icon('heroicon-o-calendar-days')
                    ->searchable()
                    ,
              
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) obra::count();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListObras::route('/'),
            'create' => Pages\CreateObra::route('/create'),
            'edit' => Pages\EditObra::route('/{record}/edit'),
        ];
    }
}
