<?php

namespace App\Filament\Resources\NewsEntriesResource\Pages;

use App\Filament\Resources\NewsEntriesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListNewsEntries extends ListRecords
{
    protected static string $resource = NewsEntriesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
