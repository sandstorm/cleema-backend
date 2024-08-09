<?php

namespace App\Filament\Resources\ChallengeTemplatesResource\Pages;

use App\Filament\Resources\ChallengeTemplatesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditChallengeTemplates extends EditRecord
{
    protected static string $resource = ChallengeTemplatesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getSavedNotification(): ?\Filament\Notifications\Notification
    {
        return \Filament\Notifications\Notification::make()->title('Challenge Template updated');
    }
}
