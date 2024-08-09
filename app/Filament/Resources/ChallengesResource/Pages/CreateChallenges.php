<?php

namespace App\Filament\Resources\ChallengesResource\Pages;

use App\Filament\Resources\ChallengesResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateChallenges extends CreateRecord
{
    protected static string $resource = ChallengesResource::class;

    protected function getCreatedNotification(): ?\Filament\Notifications\Notification
    {
        return \Filament\Notifications\Notification::make()->title('Challenge created');
    }
}
