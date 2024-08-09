<?php

namespace App\Filament\Resources\Components;

use App\Models\Challenges;
use Filament\Forms\Components\Section;
use Filament\Forms;
use Filament\Forms\Get;

class GoalTypeSelector
{
    public static function getGoalTypeSelector(bool|\Closure $isRequired = false): Section
    {
        return Section::make()
            ->columns(6)
            ->schema([
                Forms\Components\Select::make('goal_type')
                    ->columnSpan(2)
                    ->live()
                    ->options([
                        'steps' => 'steps',
                        'measurement' => 'measurement',
                    ])
                    ->native(false)
                    ->required($isRequired),
                Forms\Components\Fieldset::make('Goal Type Step')
                    ->relationship('goalTypeSteps')
                    ->hidden(function (Get $get) {
                        return $get('goal_type') !== 'steps';
                    })
                    ->schema([
                        Forms\Components\TextInput::make('count')
                            ->label('Count')
                            ->numeric()
                            ->required($isRequired)
                    ]),
                Forms\Components\Fieldset::make('Goal Type Measurement')
                    ->relationship('goalTypeMeasurement')
                    ->hidden(function (Get $get) {
                        return $get('goal_type') !== 'measurement';
                    })
                    ->schema([
                        Forms\Components\TextInput::make('value')
                            ->label('Value')
                            ->numeric()
                            ->required($isRequired),
                        Forms\Components\Select::make('unit')
                            ->label('Unit')
                            ->options([
                                'km' => 'km',
                                'm' => 'm',
                                'kg' => 'kg',
                                'g' => 'g',
                            ])
                            ->native(false)
                            ->required($isRequired),
                    ]),
            ]);
    }

}
