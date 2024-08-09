<?php

namespace App\Filament\Resources\Components;

use App\Models\Locations;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Forms\Set;

class LocationSelector
{
    public static function getLocationSelector(string $relationshipName = 'location'): Forms\Components\Section
    {
        return Forms\Components\Section::make('Location information')
            ->columns(6)
            ->schema([
                Select::make('location')
                    ->label('Name')
                    ->relationship($relationshipName, 'title')
                    ->preload()
                    ->searchable()
                    ->columnSpan(6)
                    ->afterStateUpdated(function (Set $set, $state) {
                        $set('latitude', Locations::where('id', $state)->pluck('latitude')->filter());
                        $set('longitude', Locations::where('id', $state)->pluck('longitude')->filter());
                    })
                    ->live(),
                TextInput::make('latitude')
                    ->label('Lat.*')
                    ->placeholder('Select a Location.')
                    ->columnSpan(3)
                    ->disabled()
                    ->formatStateUsing(function (Get $get) {
                        return Locations::where('id', $get('location'))->pluck('latitude')->filter();
                    }),
                TextInput::make('longitude')
                    ->label('Long.*')
                    ->placeholder('Select a Location.')
                    ->columnSpan(3)
                    ->disabled()
                    ->formatStateUsing(function (Get $get) {
                        return Locations::where('id', $get('location'))->pluck('longitude')->filter();
                    }),
            ]);
    }
}
