<?php

namespace App\Filament\Resources;

use App\Filament\Resources\Components\DateSelectors;
use App\Filament\Resources\Components\LocaleSelector;
use App\Filament\Resources\Components\QuizQuestionCreatorAndEditor;
use App\Filament\Resources\Components\RegionsSelector;
use App\Filament\Resources\QuizQuestionsResource\Pages\CreateQuizQuestions;
use App\Filament\Resources\QuizQuestionsResource\Pages\EditQuizQuestions;
use App\Filament\Resources\QuizQuestionsResource\Pages\ListQuizQuestions;
use App\Filament\Resources\QuizQuestionsResource\RelationManagers\QuizzesRelationManager;
use App\Filament\Resources\QuizzesResource\Pages;
use App\Models\QuizQuestions;
use App\Models\Quizzes;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class QuizQuestionsResource extends Resource
{
    protected static ?string $model = QuizQuestions::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(6)
            ->schema(QuizQuestionCreatorAndEditor::getModal());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('question')
                    ->label(__('Question'))
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->lineClamp(2),
                Tables\Columns\IconColumn::make('is_filler')
                    ->boolean()
                    ->label(__('Is Filler'))
                    ->alignCenter()
                    ->sortable(),
                Tables\Columns\TextColumn::make('locale')
                    ->label(__('Locale'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('region.name')
                    ->label(__('Region'))
                    ->searchable()
                    ->sortable(),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            QuizzesRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListQuizQuestions::route('/'),
            'create' => CreateQuizQuestions::route('/create'),
            'edit' => EditQuizQuestions::route('/{record}/edit'),
        ];
    }
}
