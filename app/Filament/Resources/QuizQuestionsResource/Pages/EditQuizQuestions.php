<?php

namespace App\Filament\Resources\QuizQuestionsResource\Pages;

use App\Filament\Resources\QuizQuestionsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditQuizQuestions extends EditRecord
{
    protected static string $resource = QuizQuestionsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getSavedNotification(): ?\Filament\Notifications\Notification
    {
        return \Filament\Notifications\Notification::make()->title('Quiz Question updated');
    }
}
