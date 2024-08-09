<?php

namespace App\Filament\Resources\LocationsResource\Pages;

use App\Filament\Resources\LocationsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateLocations extends CreateRecord
{
    protected static string $resource = LocationsResource::class;

    protected function getCreatedNotification(): ?\Filament\Notifications\Notification
    {
        return \Filament\Notifications\Notification::make()->title('Location created');
    }
}
