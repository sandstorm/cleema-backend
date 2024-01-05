<?php

namespace App\Filament\Resources\AdminUsersResource\Pages;

use App\Filament\Resources\AdminUsersResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAdminUsers extends CreateRecord
{
    protected static string $resource = AdminUsersResource::class;
}
