<?php

namespace App\Filament\Resources\ChallengesResource\Pages;

use App\Filament\Resources\ChallengesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditChallenges extends EditRecord
{
    protected static string $resource = ChallengesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getSavedNotification(): ?\Filament\Notifications\Notification
    {
        return \Filament\Notifications\Notification::make()->title('Challenge updated');
    }
}
