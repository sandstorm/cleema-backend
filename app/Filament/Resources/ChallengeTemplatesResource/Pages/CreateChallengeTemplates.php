<?php

namespace App\Filament\Resources\ChallengeTemplatesResource\Pages;

use App\Filament\Resources\ChallengeTemplatesResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateChallengeTemplates extends CreateRecord
{
    protected static string $resource = ChallengeTemplatesResource::class;

    protected function getCreatedNotification(): ?\Filament\Notifications\Notification
    {
        return \Filament\Notifications\Notification::make()->title('Challenge Template created');
    }
}
