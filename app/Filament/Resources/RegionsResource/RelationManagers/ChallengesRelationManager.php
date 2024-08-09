<?php

namespace App\Filament\Resources\RegionsResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ChallengesRelationManager extends RelationManager
{
    protected static string $relationship = 'challenges';
    protected static ?string $inverseRelationship = 'region';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('teaser_text')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Fieldset::make('Author')
                    ->relationship('author')
                    ->schema([
                        Forms\Components\TextInput::make('username')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->required()
                            ->maxLength(255),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('teaser_text'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AssociateAction::make()->preloadRecordSelect(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DissociateAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DissociateBulkAction::make(),
                ]),
            ]);
    }
}
