<?php

namespace App\Filament\Resources\NewsEntriesResource\Pages;

use App\Filament\Resources\NewsEntriesResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateNewsEntries extends CreateRecord
{
    protected static string $resource = NewsEntriesResource::class;

    protected function getCreatedNotification(): ?\Filament\Notifications\Notification
    {
        return \Filament\Notifications\Notification::make()->title('News Entry created');
    }
}
