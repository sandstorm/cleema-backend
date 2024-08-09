<?php

namespace App\Filament\Resources\JoinedChallengesResource\Pages;

use App\Filament\Resources\JoinedChallengesResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateJoinedChallenges extends CreateRecord
{
    protected static string $resource = JoinedChallengesResource::class;

    protected function getCreatedNotification(): ?\Filament\Notifications\Notification
    {
        return \Filament\Notifications\Notification::make()->title('Joined Challenge created');
    }

    protected function mutateFormDataBeforeCreate($data): array
    {
        //dd($data);
        return $data;
    }
}
