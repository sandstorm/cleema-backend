<?php

namespace App\Filament\Resources\AdminUsersResource\Pages;

use App\Filament\Resources\AdminUsersResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAdminUsers extends EditRecord
{
    protected static string $resource = AdminUsersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
