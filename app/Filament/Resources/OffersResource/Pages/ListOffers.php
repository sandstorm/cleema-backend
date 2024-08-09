<?php

namespace App\Filament\Resources\OffersResource\Pages;

use App\Filament\Resources\OffersResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOffers extends ListRecords
{
    protected static string $resource = OffersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
