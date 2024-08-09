<?php

namespace App\Filament\Resources\RegionsResource\Pages;

use App\Filament\Resources\RegionsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRegions extends ListRecords
{
    protected static string $resource = RegionsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
