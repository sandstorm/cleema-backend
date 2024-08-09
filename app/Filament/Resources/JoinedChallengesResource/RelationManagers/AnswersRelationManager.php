<?php

namespace App\Filament\Resources\JoinedChallengesResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AnswersRelationManager extends RelationManager
{
    protected static string $relationship = 'answers';
    protected static ?string $inverseRelationship = 'joinedChallenge';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('answer')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('day_index')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('answer')
            ->columns([
                Tables\Columns\TextColumn::make('answer'),
                Tables\Columns\TextColumn::make('day_index'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
