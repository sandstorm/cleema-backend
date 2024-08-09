<?php

namespace App\Filament\Resources\ChallengeTemplatesResource\Pages;

use App\Filament\Resources\ChallengeTemplatesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListChallengeTemplates extends ListRecords
{
    protected static string $resource = ChallengeTemplatesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
