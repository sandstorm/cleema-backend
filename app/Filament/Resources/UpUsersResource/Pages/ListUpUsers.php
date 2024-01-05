<?php

namespace App\Filament\Resources\UpUsersResource\Pages;

use App\Filament\Resources\UpUsersResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUpUsers extends ListRecords
{
    protected static string $resource = UpUsersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
