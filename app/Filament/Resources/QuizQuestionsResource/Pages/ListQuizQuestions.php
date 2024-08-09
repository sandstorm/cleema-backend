<?php

namespace App\Filament\Resources\QuizQuestionsResource\Pages;

use App\Filament\Resources\QuizQuestionsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListQuizQuestions extends ListRecords
{
    protected static string $resource = QuizQuestionsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
