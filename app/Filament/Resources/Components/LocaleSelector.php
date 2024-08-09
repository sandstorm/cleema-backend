<?php

namespace App\Filament\Resources\Components;

use Filament\Forms\Components\Select;

class LocaleSelector
{
    public static function getLocaleSelector(int $columnSpan = 1)
    {
        return Select::make('locale')
            ->columnSpan($columnSpan)
            ->options([
                'de-DE' => 'de-DE',
            ])
            ->default('de-DE')
            ->native(false);
    }
}
