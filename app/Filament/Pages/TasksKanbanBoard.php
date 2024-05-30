<?php

namespace App\Filament\Pages;

use App\Enums\TaskStatus;
use App\Models\Task;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Collection;
use Mokhosh\FilamentKanban\Pages\KanbanBoard;

class TasksKanbanBoard extends KanbanBoard
{
    
    protected static ?string $title = 'Tareas';

    protected static string $headerView = 'tasks-kanban.kanban-header';

    protected static string $recordView = 'tasks-kanban.kanban-record';

    protected static string $statusView = 'filament-kanban::kanban-status';
 
    protected static string $scriptsView = 'filament-kanban::kanban-scripts';

    protected static string $model = Task::class;

    protected static string $statusEnum = TaskStatus::class;
    protected static ?string $navigationGroup = 'Administrative';



    protected function getEditModalFormSchema(null|int $recordId): array
    {
        return [
            TextInput::make('title'),
            Textarea::make('description'),
            TextInput::make('progress')->numeric(),
        ];
    }

    protected function records(): Collection
    {
        return Task::ordered()->get();
    }

    public function onStatusChanged(int $recordId, string $status, array $fromOrderedIds, array $toOrderedIds): void
    {
        Task::find($recordId)->update(['status' => $status]);
        Task::setNewOrder($toOrderedIds);
    }

    public function onSortChanged(int $recordId, string $status, array $orderedIds): void
    {
        Task::setNewOrder($orderedIds, $status);
    }

    protected function editRecord(int $recordId, array $data, array $state): void
    {
        
        Task::find($recordId)->update([
            'title' => $data['title'],
            'description' => $data['description'],
            'progress' => $data['progress'],
        
        ]);


    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->model(Task::class)
                ->form([
                    TextInput::make('title'),
                    Textarea::make('description'),
                ])
                ->mutateFormDataUsing(function ($data) {
                    $data['user_id'] = auth()->id();

                    return $data;
                })
        ];
    }
}