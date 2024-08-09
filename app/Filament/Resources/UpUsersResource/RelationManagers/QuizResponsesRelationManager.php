<?php

namespace App\Filament\Resources\UpUsersResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class QuizResponsesRelationManager extends RelationManager
{
    protected static string $relationship = 'quizResponses';
    protected static ?string $inverseRelationship = 'responses';


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('id')
                    ->required()
                    ->numeric()
                    ->maxLength(255),
                Forms\Components\TextInput::make('question')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('answer')
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('question')
            ->columns([
                Tables\Columns\TextColumn::make('quiz_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('question')
                    ->searchable(),
                Tables\Columns\TextColumn::make('answer'),
                Tables\Columns\TextColumn::make('correct_answer'),

            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()->preloadRecordSelect(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}
