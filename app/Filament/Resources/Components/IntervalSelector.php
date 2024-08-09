<?php

namespace App\Filament\Resources\Components;

use Filament\Forms\Components\Select;

class IntervalSelector
{
    public static function getIntervalSelector(string $relationName, int $columnSpan = 2, bool|\Closure $isRequired = false)
    {
        return Select::make($relationName)
            ->options([
                'daily' => 'daily',
                //'2' => 'every other day',
                'weekly' => 'weekly',
                //'30' => 'monthly',
                //'365' => 'yearly',
            ])
            ->columnSpan($columnSpan)
            ->native(false)
            ->required($isRequired);
    }
}
