<?php

namespace App\Filament\Resources\RegionsResource\Pages;

use App\Filament\Resources\RegionsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateRegions extends CreateRecord
{
    protected static string $resource = RegionsResource::class;

    protected function getCreatedNotification(): ?\Filament\Notifications\Notification
    {
        return \Filament\Notifications\Notification::make()->title('Region created');
    }
}
