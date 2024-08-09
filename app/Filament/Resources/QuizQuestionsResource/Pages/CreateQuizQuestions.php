<?php

namespace App\Filament\Resources\QuizQuestionsResource\Pages;

use App\Filament\Resources\QuizQuestionsResource;
use Filament\Resources\Pages\CreateRecord;

class CreateQuizQuestions extends CreateRecord
{
    protected static string $resource = QuizQuestionsResource::class;

    protected function getCreatedNotification(): ?\Filament\Notifications\Notification
    {
        return \Filament\Notifications\Notification::make()->title('Quiz Question created');
    }
}
