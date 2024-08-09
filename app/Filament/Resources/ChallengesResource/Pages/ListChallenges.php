<?php

namespace App\Filament\Resources\ChallengesResource\Pages;

use App\Filament\Resources\ChallengesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListChallenges extends ListRecords
{
    protected static string $resource = ChallengesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
