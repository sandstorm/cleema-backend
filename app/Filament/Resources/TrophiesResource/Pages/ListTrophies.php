<?php

namespace App\Filament\Resources\TrophiesResource\Pages;

use App\Filament\Resources\TrophiesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTrophies extends ListRecords
{
    protected static string $resource = TrophiesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
