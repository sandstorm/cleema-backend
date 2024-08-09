<?php

namespace App\Filament\Resources\AdminRolesResource\Pages;

use App\Filament\Resources\AdminRolesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAdminRoles extends ListRecords
{
    protected static string $resource = AdminRolesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
