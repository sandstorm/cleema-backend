<?php

namespace App\Filament\Resources\TrophiesResource\Pages;

use App\Filament\Resources\TrophiesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTrophies extends EditRecord
{
    protected static string $resource = TrophiesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getSavedNotification(): ?\Filament\Notifications\Notification
    {
        return \Filament\Notifications\Notification::make()->title('Trophy updated');
    }
}
