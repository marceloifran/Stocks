<?php

namespace App\Filament\Resources;

use Closure;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use App\Models\personal;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Contracts\View\View;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\Layout\Split;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\TextEntry;
use App\Filament\Resources\PersonalResource\Pages;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use Saade\FilamentAutograph\Forms\Components\SignaturePad;
use App\Filament\Resources\PersonalResource\RelationManagers;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use App\Filament\Resources\PersonalResource\Widgets\PersonOverview;
use Saade\FilamentAutograph\Forms\Components\Enums\DownloadableFormat;
use Hugomyb\FilamentMediaAction\Infolists\Components\Actions\MediaAction;

class PersonalResource extends Resource
{
    protected static ?string $model = personal::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Personal';
    protected static ?string $navigationGroup = 'Administrativo';
    protected static  ?string $recordTitleAttribute = 'nombre';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->label(trans('form.name'))
                    ->placeholder(__(trans('form.name'))),
                Forms\Components\Select::make('obra_id')
                    ->label(__('Obra'))
                    ->relationship('obra', 'nombre')
                    ->required(),
                Forms\Components\TextInput::make('dni')
                    ->unique(ignoreRecord: true)
                    ->numeric()
                    ->placeholder(__('DNI')),
                Forms\Components\TextInput::make('cargo')
                    ->required()
                    ->placeholder(__('Cargo')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Split::make([
                    TextColumn::make('nombre')
                        ->searchable()
                        ->icon('heroicon-o-user')
                        ->label(trans('tables.name')),
                    TextColumn::make('dni')
                        ->searchable(),
                    TextColumn::make('obra.nombre')
                        ->searchable()
                        ->icon('heroicon-o-building-office-2'),
                ])
                    ->from('md')
            ])
            ->defaultSort('nombre', 'asc')
            ->filters([])
            ->actions([

                Tables\Actions\Action::make('299')
                    ->url(fn(personal $record) => route('personal.exportPdf', $record->id))
                    ->icon('heroicon-o-eye'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    ExportBulkAction::make()

                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // RelationManagers\StockMoventRelationManager::class,
        ];
    }

    public static function getWidgets(): array
    {
        return [];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) Personal::count();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPersonals::route('/'),
            'create' => Pages\CreatePersonal::route('/create'),
            'edit' => Pages\EditPersonal::route('/{record}/edit'),
        ];
    }
}
