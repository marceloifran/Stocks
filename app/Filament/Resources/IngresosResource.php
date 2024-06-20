<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use App\Models\ingresos;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\IngresosResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Saade\FilamentAutograph\Forms\Components\SignaturePad;
use App\Filament\Resources\IngresosResource\RelationManagers;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Saade\FilamentAutograph\Forms\Components\Enums\DownloadableFormat;

class IngresosResource extends Resource
{
    protected static ?string $model = ingresos::class;

    protected static ?string $navigationIcon = 'heroicon-o-paper-airplane';
    protected static ?string $navigationLabel = 'Ingresos';
    protected static ?string $navigationGroup = 'Administrative';
    

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Step::make('Informacion Personal') 
                    ->schema([
                        Forms\Components\TextInput::make('nombre')
                        ->autofocus()
                        ->disabledOn('edit') 
                        // ->rules([
                        //     fn (Get $get): Closure => function ($attribute, $value, $fail) use ($get) {
                        //         if ($get('nombre')) {
                        //             $personal = personal::where('nombre', $value)->first();
                        //             if ($personal && $personal->nombre == $value) {
                        //                 $fail(__('Ya existe una persona con el mismo nombre'));
                        //             }
                        //         }
                        //     },
                        // ])
                        ->unique(ignoreRecord:true)
                        ->required()
                        ->label('Nombre Completo')
                        ->placeholder(__('Nombre Completo'))->required(),
                        DatePicker::make('fecha')
                        ->autofocus()
                        ->disabledOn('edit') 
                        ->required()
                        ->label('Fecha de Entrada')
                        ->default(Carbon::now()),
                        Forms\Components\TextInput::make('dni')
                        ->autofocus()
                        ->disabledOn('edit') 
                        // ->rules([
                        //     fn (Get $get): Closure => function ($attribute, $value, $fail) use ($get) {
                        //         if ($get('dni')) {
                        //             $ingresos = ingresos::where('dni', $value)->first();
                        //             if ($personal && $personal->dni == $value) {
                        //                 $fail(__('El dni ya está en uso, elija otro'));
                        //             }
                        //         }
                        //     },
                        // ])
                        ->numeric()
                        ->placeholder(__('Dni')),
                    ])
                    ->icon('heroicon-o-user')
                ])
               ,
               
                Wizard::make([
                    Step::make('Firma Digital') ->schema([
                        SignaturePad::make('firma')
                        // ->required()
                        ->label('Firma')
                        ->downloadableFormats([
                            DownloadableFormat::PNG,
                            DownloadableFormat::JPG,
                            DownloadableFormat::SVG,
                        ])
                        
                        ->backgroundColor('#FFFFFF')  // Background color on light mode
                        ->backgroundColorOnDark('#FFFFFF')     // Background color on dark mode (defaults to backgroundColor)
                        ->exportBackgroundColor('#FFFFFF')     // Background color on export (defaults to backgroundColor)
                        ->penColor('#040404')                  // Pen color on light mode
                        ->penColorOnDark('#040404')            // Pen color on dark mode (defaults to penColor)
                        ->exportPenColor('#040404') ,
                    ])
                    ->icon('heroicon-o-pencil-square')
                ]),
               
               
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('dni')
                    ->searchable()
                    ->sortable(),
                // Tables\Columns\TextColumn::make('firma')
                //     ->searchable()
                //     ->sortable(),
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
            'index' => Pages\ListIngresos::route('/'),
            'create' => Pages\CreateIngresos::route('/create'),
            'edit' => Pages\EditIngresos::route('/{record}/edit'),
        ];
    }
}
