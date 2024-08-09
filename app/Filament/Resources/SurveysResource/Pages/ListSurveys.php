<?php

namespace App\Filament\Resources\SurveysResource\Pages;

use App\Filament\Resources\SurveysResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSurveys extends ListRecords
{
    protected static string $resource = SurveysResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
