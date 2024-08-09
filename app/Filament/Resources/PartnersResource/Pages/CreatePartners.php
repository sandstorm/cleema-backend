<?php

namespace App\Filament\Resources\PartnersResource\Pages;

use App\Filament\Resources\PartnersResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePartners extends CreateRecord
{
    protected static string $resource = PartnersResource::class;

    protected function getCreatedNotification(): ?\Filament\Notifications\Notification
    {
        return \Filament\Notifications\Notification::make()->title('Partner created');
    }
}
