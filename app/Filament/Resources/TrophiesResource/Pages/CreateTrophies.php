<?php

namespace App\Filament\Resources\TrophiesResource\Pages;

use App\Filament\Resources\TrophiesResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTrophies extends CreateRecord
{
    protected static string $resource = TrophiesResource::class;

    protected function getCreatedNotification(): ?\Filament\Notifications\Notification
    {
        return \Filament\Notifications\Notification::make()->title('Trophy created');

    }
}
