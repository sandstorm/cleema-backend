<?php

namespace App\Filament\Resources\JoinedChallengesResource\Pages;

use App\Filament\Resources\JoinedChallengesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListJoinedChallenges extends ListRecords
{
    protected static string $resource = JoinedChallengesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
