<?php

namespace App\Filament\Resources\OffersResource\Pages;

use App\Filament\Resources\OffersResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateOffers extends CreateRecord
{
    protected static string $resource = OffersResource::class;

    protected function getCreatedNotification(): ?\Filament\Notifications\Notification
    {
        return \Filament\Notifications\Notification::make()->title('Offer created');
    }
}
