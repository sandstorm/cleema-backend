<?php

namespace App\Filament\Resources\SurveysResource\Pages;

use App\Filament\Resources\SurveysResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSurveys extends CreateRecord
{
    protected static string $resource = SurveysResource::class;

    protected function getCreatedNotification(): ?\Filament\Notifications\Notification
    {
        return \Filament\Notifications\Notification::make()->title('Survey created');
    }
}
