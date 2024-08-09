<?php

namespace App\Filament\Resources\Components;

use App\Models\Regions;
use Filament\Forms;

class RegionsSelector
{
    public static function getRegionsSelector(
        int $columnSpan = 2,
        string $label = 'Regions',
        bool|\Closure $hidden = false ): Forms\Components\Select
    {
        return Forms\Components\Select::make('region')
            ->label(__($label))
            ->relationship('region')
            ->options(self::getRegions())
            ->preload()
            ->searchable()
            ->columnSpan($columnSpan)
            ->native(false)
            ->required()
            ->hidden($hidden);
    }

    private static function getRegions(): array
    {
        $regions = Regions::all()->pluck('name', 'id')->filter()->toArray();
        $supraregionalRegion = Regions::where('is_supraregional', true)->first();
        // UX: Universal Region should not be somewhere in between the regions, so we push it to the top of the select
        if ($supraregionalRegion != null) {
            $universalRegionIndex = array_search($supraregionalRegion->name, $regions);
            if ($universalRegionIndex) {
                $universal = $regions[$universalRegionIndex];
                unset($regions[$universalRegionIndex]);
                $regions = [$universalRegionIndex => $universal] + $regions;
            }
        }
        return $regions;
    }

}
