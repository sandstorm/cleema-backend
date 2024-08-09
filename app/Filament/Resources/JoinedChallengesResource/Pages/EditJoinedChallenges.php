<?php

namespace App\Filament\Resources\JoinedChallengesResource\Pages;

use App\Filament\Resources\JoinedChallengesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJoinedChallenges extends EditRecord
{
    protected static string $resource = JoinedChallengesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getSavedNotification(): ?\Filament\Notifications\Notification
    {
        return \Filament\Notifications\Notification::make()->title('Joined Challenge updated');
    }

    protected function mutateFormDataBeforeSave($data): array
    {
        //dd($data);
        return $data;
    }
}
