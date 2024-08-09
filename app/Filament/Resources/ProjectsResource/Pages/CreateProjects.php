<?php

namespace App\Filament\Resources\ProjectsResource\Pages;

use App\Filament\Resources\ProjectsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProjects extends CreateRecord
{
    protected static string $resource = ProjectsResource::class;

    protected function getCreatedNotification(): ?\Filament\Notifications\Notification
    {
        return \Filament\Notifications\Notification::make()->title('Project created');
    }
}
