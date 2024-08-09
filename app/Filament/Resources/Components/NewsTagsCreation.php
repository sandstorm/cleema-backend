<?php

namespace App\Filament\Resources\Components;

use Filament\Forms\Components\TextInput;

class NewsTagsCreation
{
    public static function getModal()
    {
        return [
            TextInput::make('value')
                ->label('Name')
                ->required()
                ->maxLength(255),
        ];
    }
}
