<?php

namespace App\Filament\Resources\UpUsersResource\Pages;

use App\Filament\Resources\UpUsersResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUpUsers extends EditRecord
{
    protected static string $resource = UpUsersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
