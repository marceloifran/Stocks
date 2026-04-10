<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuotationResource\Pages;
use App\Filament\Resources\QuotationResource\RelationManagers;
use App\Models\Quotation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Services\QuotationParserService;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class QuotationResource extends Resource
{
    protected static ?string $model = Quotation::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-magnifying-glass';
    protected static ?string $navigationGroup = 'Gestión de Compras';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detalles de la Cotización')
                    ->icon('heroicon-o-currency-dollar')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('purchase_request_id')
                                    ->label('Solicitud de Compra')
                                    ->relationship('purchaseRequest', 'product_name')
                                    ->required(),
                                Forms\Components\Select::make('supplier_id')
                                    ->label('Proveedor')
                                    ->relationship('supplier', 'name')
                                    ->required(),
                            ]),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('price')
                                    ->label('Precio Cotizado')
                                    ->numeric()
                                    ->prefix('$')
                                    ->required(),
                                Forms\Components\TextInput::make('delivery_time')
                                    ->label('Tiempo de Entrega')
                                    ->placeholder('Ej: 48hs, 1 semana...')
                                    ->maxLength(255),
                            ]),
                        Forms\Components\Toggle::make('parsed')
                            ->label('Procesado por IA')
                            ->default(false),
                    ]),
                Forms\Components\Section::make('Contenido del Email')
                    ->description('Texto original recibido del proveedor')
                    ->collapsed()
                    ->schema([
                        Forms\Components\Textarea::make('raw_email_text')
                            ->label('Cuerpo del Mensaje')
                            ->rows(10)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('purchaseRequest.product_name')
                    ->label('Producto')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('supplier.name')
                    ->label('Proveedor')
                    ->sortable()
                    ->searchable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('price')
                    ->label('Precio')
                    ->money('USD')
                    ->sortable()
                    ->color('success')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('delivery_time')
                    ->label('Entrega')
                    ->searchable()
                    ->icon('heroicon-m-truck'),
                Tables\Columns\IconColumn::make('parsed')
                    ->label('IA')
                    ->boolean()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Recibida')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('parse_ai')
                    ->label('Analizar con IA')
                    ->icon('heroicon-o-sparkles')
                    ->color('info')
                    ->action(function (Quotation $record, QuotationParserService $service) {
                        if (!$record->raw_email_text) {
                            Notification::make()
                                ->title('No hay texto para analizar')
                                ->warning()
                                ->send();
                            return;
                        }

                        $data = $service->parse($record->raw_email_text);
                        
                        $record->update([
                            'price' => $data['price'],
                            'delivery_time' => $data['delivery_time'],
                            'parsed' => true,
                        ]);

                        Notification::make()
                            ->title('Análisis completado')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Quotation $record) => !$record->parsed),
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('purchaseRequest', function (Builder $query) {
                $query->where('company_id', auth()->user()->company_id);
            });
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuotations::route('/'),
            'create' => Pages\CreateQuotation::route('/create'),
            'edit' => Pages\EditQuotation::route('/{record}/edit'),
        ];
    }
}
