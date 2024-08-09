<?php

namespace App\Filament\Resources\OffersResource\Pages;

use App\Filament\Resources\OffersResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOffers extends EditRecord
{
    protected static string $resource = OffersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getSavedNotification(): ?\Filament\Notifications\Notification
    {
        return \Filament\Notifications\Notification::make()->title('Offer updated');
    }
}
