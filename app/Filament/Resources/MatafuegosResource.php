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

    protected static ?string $navigationIcon = 'heroicon-o-fire';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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
                    ->label('Capacidad')
                    ->placeholder(__('Capacidad')),
              
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
                Tables\Actions\EditAction::make(),
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
