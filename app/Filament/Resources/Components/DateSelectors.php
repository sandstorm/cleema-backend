<?php

namespace App\Filament\Resources\Components;

use Filament\Forms\Set;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\DatePicker;

class DateSelectors
{
    public static function getPublishedAtDateSelector(int $columnSpan = 2): DateTimePicker
    {
        return DateTimePicker::make('published_at')
            ->columnSpan($columnSpan)
            ->live()
            ->default(function ($state) {
                return date('y-m-d T H:i:s');
            })
            ->timezone('Europe/Berlin');
    }
}
