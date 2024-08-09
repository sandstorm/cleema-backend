<?php

namespace App\Filament\Resources\Components;

use App\Models\Partners;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;

class PartnersSelector
{
    public static function getPartnersSelector(bool|\Closure $isHidden = false, bool|\Closure $isRequired = false)
    {
        return Section::make()
            ->columns(6)
            ->hidden($isHidden)
            ->schema([
                Select::make('partner')
                    ->label('Partner')
                    ->relationship('partner')
                    ->options(Partners::query()->pluck('title', 'id')->filter())
                    ->preload()
                    ->searchable()
                    ->columnSpan(3)
                    ->afterStateUpdated(function (Set $set, $state) {
                        $set('url', Partners::where('id', $state)->pluck('url')->filter());
                        $set('partner_description', Partners::where('id', $state)->pluck('description')->filter());
                    })
                    ->live()
                    ->required($isRequired),
                TextInput::make('url')
                    ->label('Website')
                    ->placeholder('Select a Partner to see their website.')
                    ->columnSpan(3)
                    ->disabled()
                    ->formatStateUsing(function (Get $get) {
                        return Partners::where('id', $get('partner'))->pluck('url')->filter();
                    }),
                Textarea::make('partner_description')
                    ->label('Description')
                    ->placeholder('Select a Partner to see their description.')
                    ->columnSpanFull()
                    ->disabled()
                    ->formatStateUsing(function (Get $get) {
                        return Partners::where('id', $get('partner'))->pluck('description')->filter();
                    }),
            ]);
    }
}
