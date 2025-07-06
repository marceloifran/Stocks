<?php

namespace App\Filament\Pages;

use App\Models\PurchaseOrder;
use App\Models\Stock;
use Filament\Actions\Action;
use Filament\Forms;
use Illuminate\Database\Eloquent\Builder;
use Relaticle\Flowforge\Filament\Pages\KanbanBoardPage;
use Carbon\Carbon;

class PurchaseOrdersBoardBoardPage extends KanbanBoardPage
{
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationLabel = 'Órdenes de Compra';
    protected static ?string $title = 'Gestión de Órdenes de Compra';
    protected static ?string $navigationGroup = 'Stocks';
    protected static ?int $navigationSort = 3;

    public function getSubject(): Builder
    {
        return PurchaseOrder::query();
    }

    public function mount(): void
    {
        $this
            ->titleField('id')
            ->descriptionField('notes')
            ->orderField('order_column')
            ->columnField('status')
            ->columns([
                'pendiente' => 'Pendiente',
                'pedido' => 'Pedido',
                'comprado' => 'Comprado',
                'recibido' => 'Recibido',
            ])
            ->columnColors([
                'pendiente' => 'red',
                'pedido' => 'orange',
                'comprado' => 'blue',
                'recibido' => 'green',
            ])
            ->cardLabel('Orden')
            ->pluralCardLabel('Órdenes')
            ->cardAttributes([
                'stock.nombre' => 'Producto',
                'quantity' => 'Cantidad',
                'requested_date' => 'Fecha de solicitud',
            ])
            ->cardAttributeIcons([
                'stock.nombre' => 'heroicon-o-cube',
                'quantity' => 'heroicon-o-calculator',
                'requested_date' => 'heroicon-o-calendar',
            ])
            ->enableDragAndDrop()
            ->enableColumnDragAndDrop()
            ->enableCardDragAndDrop()
            ->enableColumnReordering();
    }

    public function createAction(Action $action): Action
    {
        return $action
            ->iconButton()
            ->icon('heroicon-o-plus')
            ->modalHeading('Crear Orden de Compra')
            ->modalWidth('xl')
            ->form(function (Forms\Form $form) {
                return $form->schema([
                    Forms\Components\Select::make('stock_id')
                        ->label('Producto')
                        ->options(Stock::all()->pluck('nombre', 'id'))
                        ->searchable()
                        ->required(),
                    Forms\Components\TextInput::make('quantity')
                        ->label('Cantidad')
                        ->numeric()
                        ->default(1)
                        ->required(),
                    Forms\Components\Textarea::make('notes')
                        ->label('Notas')
                        ->columnSpanFull(),
                    Forms\Components\DatePicker::make('requested_date')
                        ->label('Fecha de solicitud')
                        ->default(Carbon::now())
                        ->required(),
                ]);
            });
    }

    public function editAction(Action $action): Action
    {
        return $action
            ->modalHeading('Editar Orden de Compra')
            ->modalWidth('xl')
            ->form(function (Forms\Form $form) {
                return $form->schema([
                    Forms\Components\Select::make('stock_id')
                        ->label('Producto')
                        ->options(Stock::all()->pluck('nombre', 'id'))
                        ->searchable()
                        ->required(),
                    Forms\Components\TextInput::make('quantity')
                        ->label('Cantidad')
                        ->numeric()
                        ->required(),
                    Forms\Components\Select::make('status')
                        ->label('Estado')
                        ->options([
                            'pendiente' => 'Pendiente',
                            'pedido' => 'Pedido',
                            'comprado' => 'Comprado',
                            'recibido' => 'Recibido',
                        ])
                        ->required(),
                    Forms\Components\Textarea::make('notes')
                        ->label('Notas')
                        ->columnSpanFull(),
                    Forms\Components\DatePicker::make('requested_date')
                        ->label('Fecha de solicitud')
                        ->required(),
                    Forms\Components\DatePicker::make('ordered_date')
                        ->label('Fecha de pedido')
                        ->visible(fn(Forms\Get $get) => in_array($get('status'), ['pedido', 'comprado', 'recibido'])),
                    Forms\Components\DatePicker::make('received_date')
                        ->label('Fecha de recepción')
                        ->visible(fn(Forms\Get $get) => in_array($get('status'), ['recibido'])),
                ]);
            });
    }
}
