<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PurchaseRequestResource\Pages;
use App\Filament\Resources\PurchaseRequestResource\RelationManagers;
use App\Models\PurchaseRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\WhatsAppTemplate;
use Filament\Notifications\Notification;
use App\Services\SendQuotationRequestService;
use Rarq\FilamentWhatsappMessagePreview\Forms\Components\WhatsappMessagePreview;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PurchaseRequestResource extends Resource
{
    protected static ?string $model = PurchaseRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Gestión de Compras';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Tabs')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Información del Pedido')
                            ->icon('heroicon-m-shopping-cart')
                            ->schema([
                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        Forms\Components\TextInput::make('product_name')
                                            ->label('Producto / Servicio')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('quantity')
                                            ->label('Cantidad')
                                            ->numeric()
                                            ->required(),
                                        Forms\Components\Select::make('status')
                                            ->label('Estado')
                                            ->options([
                                                'draft' => 'Borrador',
                                                'sent' => 'Enviado',
                                                'completed' => 'Completado',
                                            ])
                                            ->default('draft')
                                            ->required(),
                                    ]),
                                Forms\Components\Textarea::make('notes')
                                    ->label('Notas Adicionales')
                                    ->columnSpanFull(),
                            ]),
                        Forms\Components\Tabs\Tab::make('Proveedores Seleccionados')
                            ->icon('heroicon-m-user-group')
                            ->schema([
                                Forms\Components\Select::make('suppliers')
                                    ->label('Seleccionar Proveedores')
                                    ->multiple()
                                    ->relationship('suppliers', 'name')
                                    ->preload()
                                    ->required(),
                                Forms\Components\Placeholder::make('hint')
                                    ->content('Selecciona los proveedores a los que deseas enviar el pedido de cotización.'),
                            ]),
                        Forms\Components\Tabs\Tab::make('Vista Previa WhatsApp')
                            ->icon('heroicon-m-chat-bubble-left-right')
                            ->schema([
                                Forms\Components\Select::make('whats_app_template_id')
                                    ->label('Seleccionar Plantilla')
                                    ->options(WhatsAppTemplate::all()->pluck('name', 'id'))
                                    ->searchable()
                                    ->live()
                                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                        $template = WhatsAppTemplate::find($state);
                                        if ($template) {
                                            $set('whatsapp_preview', $template->parse([
                                                'product_name' => $get('product_name') ?? '...',
                                                'quantity' => $get('quantity') ?? '...',
                                            ]));
                                        }
                                    }),

                                WhatsappMessagePreview::make('whatsapp_preview')
                                    ->label('Mensaje a enviar')
                                    ->reactive()
                                    ->readOnly(),
                            ]),
                    ])->columnSpanFull(),

                Forms\Components\Section::make('Administración')
                    ->collapsed()
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('company_id')
                                    ->relationship('company', 'name')
                                    ->required()
                                    ->default(auth()->user()->company_id)
                                    ->disabled(),
                                Forms\Components\Select::make('user_id')
                                    ->relationship('user', 'name')
                                    ->required()
                                    ->default(auth()->id())
                                    ->disabled(),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product_name')
                    ->label('Producto')
                    ->searchable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Cant.')
                    ->numeric()
                    ->sortable()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->searchable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'sent' => 'warning',
                        'completed' => 'success',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'draft' => 'heroicon-m-pencil-square',
                        'sent' => 'heroicon-m-paper-airplane',
                        'completed' => 'heroicon-m-check-circle',
                    }),
                Tables\Columns\TextColumn::make('suppliers_count')
                    ->label('Prov.')
                    ->counts('suppliers')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('send_requests')
                    ->label('Enviar solicitudes')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->action(function (PurchaseRequest $record, SendQuotationRequestService $service) {
                        $service->send($record);
                        Notification::make()
                            ->title('Solicitudes enviadas correctamente')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->visible(fn (PurchaseRequest $record) => $record->status === 'draft'),
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
            ->where('company_id', auth()->user()->company_id);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPurchaseRequests::route('/'),
            'create' => Pages\CreatePurchaseRequest::route('/create'),
            'edit' => Pages\EditPurchaseRequest::route('/{record}/edit'),
        ];
    }
}
