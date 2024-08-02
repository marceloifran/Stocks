<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\matafuegos;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\MatafuegosResource\Pages;
use App\Filament\Resources\MatafuegosResource\RelationManagers;

class MatafuegosResource extends Resource
{
    protected static ?string $model = matafuegos::class;

    protected static ?string $navigationGroup = 'Administrative';


    protected static ?string $navigationIcon = 'heroicon-o-fire';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
               DatePicker::make('fecha_fabricacion')
                    ->autofocus()
                    ->required()
                    ->label('Fecha de Fabricacion')
                    ->placeholder(__('Fecha de Fabricacion')),
               DatePicker::make('fecha_ultima_recarga')
                    ->autofocus()
                    ->required()
                    ->label('Fecha de ultima Recarga')
                    ->placeholder(__('Fecha de recarga')),
               DatePicker::make('fecha_vencimiento')
                    ->autofocus()
                    ->required()
                    ->label('Fecha de Vencimiento')
                    ->placeholder(__('Fecha de Vencimiento')),
                Forms\Components\TextInput::make('ubicacion')
                    ->autofocus()
                    ->required()
                    ->label('Ubicacion')
                    ->placeholder(__('Ubicacion')),
                Forms\Components\TextInput::make('capacidad')
                    ->autofocus()
                    ->required()
                    ->label('Capacidad en kg')
                    ->placeholder(__('Capacidad')),
                Forms\Components\TextInput::make('responsable_mantenimiento')
                    ->autofocus()
                    ->required()
                    ->label('Responsable de Mantenimiento')
                    ->placeholder(__('Responsable de Mantenimiento')),
                Forms\Components\TextInput::make('numero_serie')
                    ->autofocus()
                    ->required()
                    ->label('Numero de Serie')
                    ->placeholder(__('Numero de Serie')),
              
            ]);
    }
    
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('fecha_vencimiento')->label('Fecha de Vencimiento'),
                Tables\Columns\TextColumn::make('ubicacion')->label('Ubicacion'),
                Tables\Columns\TextColumn::make('capacidad')->label('Capacidad'),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                Action::make('Ver Qr')
                ->icon('heroicon-o-qr-code')
                ->url(fn(matafuegos $record): string => static::getUrl('qr-code', ['record' => $record])),
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
        return (string) matafuegos::count();
    }   

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMatafuegos::route('/'),
            'create' => Pages\CreateMatafuegos::route('/create'),
            'edit' => Pages\EditMatafuegos::route('/{record}/edit'),
            'qr-code' => Pages\ViewQrCode::route('/{record}/qr-code'),

        ];
    }
}
