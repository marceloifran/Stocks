<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\personal;
use Filament\Forms\Form;
use App\Models\Vacaciones;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\VacacionesResource\Pages;
use TangoDevIt\FilamentEmojiPicker\EmojiPickerAction;
use App\Filament\Resources\VacacionesResource\RelationManagers;

class VacacionesResource extends Resource
{
    protected static ?string $model = Vacaciones::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';
    protected static ?string $navigationGroup = 'Administrative';
    protected static ?string $navigationLabel = 'Vacaciones';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('personal_id')
                ->options( personal::all()->pluck('nombre', 'id'))
                 ->searchable()
                 ->label('Personal')
                 ->required(),
                Forms\Components\DatePicker::make('fecha_inicio')
                    ->autofocus()
                    ->required()
                    ->label('Fecha Inicio'),
                Forms\Components\DatePicker::make('fecha_fin')
                    ->autofocus()
                    ->required()
                    ->label('Fecha Fin'),
                Forms\Components\textarea::make('comentario')
                    ->autofocus()
                    ->required()
                    ->label('Comentario'),
                Forms\Components\TextInput::make('estado')
                    ->autofocus()
                    ->suffixAction(EmojiPickerAction::make('emoji-title'))
                    ->required()
                    ->label('Estado'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListVacaciones::route('/'),
            'create' => Pages\CreateVacaciones::route('/create'),
            'edit' => Pages\EditVacaciones::route('/{record}/edit'),
        ];
    }
}
