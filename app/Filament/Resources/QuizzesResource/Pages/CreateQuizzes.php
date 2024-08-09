<?php

namespace App\Filament\Resources\QuizzesResource\Pages;

use App\Filament\Resources\QuizzesResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateQuizzes extends CreateRecord
{
    protected static string $resource = QuizzesResource::class;

    protected function getCreatedNotification(): ?\Filament\Notifications\Notification
    {
        return \Filament\Notifications\Notification::make()->title('Quiz created');
    }
}
