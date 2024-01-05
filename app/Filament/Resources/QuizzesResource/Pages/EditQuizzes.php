<?php

namespace App\Filament\Resources\QuizzesResource\Pages;

use App\Filament\Resources\QuizzesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditQuizzes extends EditRecord
{
    protected static string $resource = QuizzesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
