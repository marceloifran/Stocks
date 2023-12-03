<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use App\Models\tarea;
use App\Models\proyecto;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\TareaResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\TareaResource\RelationManagers;

class TareaResource extends Resource
{
    protected static ?string $model = tarea::class;

    protected static ?string $navigationIcon = 'heroicon-o-adjustments-vertical';
    protected static ?string $navigationGroup = 'Management';
    protected static ?string $navigationLabel = 'Tasks';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('Nombre')
                ->autofocus()
                ->required()
                ->placeholder(__('Nombre'))
                ->required()
               ,
                Forms\Components\Textarea::make('descripcion')
                ->autofocus()
                ->placeholder(__('Descripcion'))
                ->nullable(),
                Select::make('proyecto_id')
                ->options( proyecto::all()->pluck('nombre', 'id'))
                  ->required()
                  ->label('Proyecto')
                  ->searchable()
                  ->required(),
                  Forms\Components\DatePicker::make('fecha_limite')
                  ->autofocus()
                  ->required()
                  ->label('Fecha Limite')
                  ->default(Carbon::now()),
                    Select::make('estado')
                    ->options([
                        'Pendiente' => 'Pendiente' ,
                        'Proceso' => 'Proceso' ,
                        'Finalizado' => 'Finalizado' ,
                    ])
                    ->searchable()


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('proyecto.nombre')
                ->searchable()
                ->sortable()
                ->copyable(),
                Tables\Columns\TextColumn::make('nombre')
                ->searchable()
                ->sortable()
                ->copyable(),
                Tables\Columns\TextColumn::make('fecha_limite')
                ->searchable()
                ->sortable()
                ->copyable(),
                Tables\Columns\TextColumn::make('estado')
                ->searchable()
                ->sortable()
                ->copyable(),
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
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
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
            'index' => Pages\ListTareas::route('/'),
            'create' => Pages\CreateTarea::route('/create'),
            'edit' => Pages\EditTarea::route('/{record}/edit'),
        ];
    }
}
