<?php

namespace App\Filament\Resources\QuizQuestionsResource\RelationManagers;

use App\Filament\Resources\QuizzesResource;
use App\Models\Quizzes;
use Carbon\Carbon;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class QuizzesRelationManager extends RelationManager
{
    protected static string $relationship = 'quizzes';

    public function form(Form $form): Form
    {
        return QuizzesResource::form($form);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('text')
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('date')
                    ->label(__('Publish Date'))
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(function (Quizzes $quizzes) {
                        return Carbon::make($quizzes->date)->toDateString();
                    }),
            ])
            ->filters([])
            ->headerActions([])
            ->actions([
                Tables\Actions\ViewAction::make('view'),
                Tables\Actions\Action::make('go to quiz')
                    ->iconButton()
                    ->icon('heroicon-o-arrow-up-right')
                    ->action(fn(Quizzes $quizzes) => redirect(QuizzesResource\Pages\EditQuizzes::getUrl([
                        'record' => $quizzes->getRouteKey(),
                    ]))),
            ])
            ->bulkActions([]);
    }
}
