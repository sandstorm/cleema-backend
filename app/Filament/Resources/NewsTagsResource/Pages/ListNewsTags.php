<?php

namespace App\Filament\Resources\NewsTagsResource\Pages;

use App\Filament\Resources\Components\NewsTagsCreation;
use App\Filament\Resources\NewsTagsResource;
use App\Models\NewsTags;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListNewsTags extends ListRecords
{
    protected static string $resource = NewsTagsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->modal(NewsTags::class)
                ->form(NewsTagsCreation::getModal())
        ];
    }
}
