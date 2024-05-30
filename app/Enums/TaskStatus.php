<?php

namespace App\Enums;

use Mokhosh\FilamentKanban\Concerns\IsKanbanStatus;

enum TaskStatus: string
{
    use IsKanbanStatus;

    case Todo = 'Todo';
    case Doing = 'doing';
    case Done = 'done';

    public function getTitle(): string
    {
        return $this->name;
    }
}
