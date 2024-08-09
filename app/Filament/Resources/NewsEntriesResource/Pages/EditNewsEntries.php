<?php

namespace App\Filament\Resources\NewsEntriesResource\Pages;

use App\Filament\Resources\NewsEntriesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditNewsEntries extends EditRecord
{
    protected static string $resource = NewsEntriesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getSavedNotification(): ?\Filament\Notifications\Notification
    {
        return \Filament\Notifications\Notification::make()->title('News Entry updated');
    }
}
