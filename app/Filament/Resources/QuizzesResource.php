<?php

namespace App\Filament\Resources;

use App\Filament\Resources\Components\DateSelectors;
use App\Filament\Resources\Components\LocaleSelector;
use App\Filament\Resources\Components\QuizQuestionCreatorAndEditor;
use App\Filament\Resources\QuizzesResource\Pages;
use App\Filament\Resources\QuizzesResource\RelationManagers\ResponsesRelationManager;
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

class QuizzesResource extends Resource
{
    protected static ?string $model = Quizzes::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->columns(6)
                    ->schema([
                        Section::make()
                            ->columns(6)
                            ->schema([
                                Forms\Components\Select::make('quiz_question_id')
                                    ->relationship('quizQuestion', 'question')
                                    ->preload()
                                    ->createOptionForm(QuizQuestionCreatorAndEditor::getModal(true))
                                    ->editOptionForm(QuizQuestionCreatorAndEditor::getModal(true))
                                    ->searchable()
                                    ->columnSpan(6)
                                    ->live()
                                    ->required()
                                    ->afterStateUpdated(function (Set $set, $state) {
                                        $quizQuestion = QuizQuestions::where('id', '=', $state)->first();

                                        if($quizQuestion != null) {
                                            $set('answers', $quizQuestion->answers()->get()->toArray());
                                            $set('correct_answer', $quizQuestion->correct_answer ?? ' ');
                                            $set('explanation', $quizQuestion->explanation ?? ' ');
                                        }
                                    }),
                                Forms\Components\Repeater::make('answers')
                                    ->columnSpanFull()
                                    ->grid(2)
                                    ->defaultItems(2)
                                    ->minItems(2)
                                    // full alphabet
                                    ->disabled()
                                    ->maxItems(26)
                                    ->addActionLabel('Add Answer')
                                    ->formatStateUsing(function (Get $get) {
                                        $quizQuestion = QuizQuestions::where('id', $get('quiz_question_id'))->get()->first();
                                        if ($quizQuestion == null) return [];
                                        return $quizQuestion->answers()->get();
                                    })
                                    ->itemLabel(function ($uuid, $component) {
                                        $keys = array_keys($component->getState());
                                        $index = array_search($uuid, $keys);
                                        $alphabet = range('a', 'z');
                                        return $alphabet[$index];
                                    })
                                    ->deleteAction(function (Action $action) {
                                        $action->after(function (Get $get, Set $set) {
                                            $answers = $get('answers');
                                            $alphabet = range('a', 'z');
                                            foreach (array_keys($answers) as $index => $key) {
                                                $answers[$key]['option'] = $alphabet[$index];
                                            }
                                            $set('answers', $answers);
                                        });
                                    })
                                    ->schema(
                                        [
                                            Forms\Components\Hidden::make('option')
                                                ->live(),
                                            /*
                                             * we use a Hidden here because we if we use a TextInput and disable or hide it
                                             * the state won't get sent to the db
                                             * Since the Item Label already shows what option it is, we can use a Hidden
                                             * But setting the state with `->state()` doesn't work because of issues during
                                             * initialization and the option text below is required, we just update the
                                             * option state there
                                            */
                                            Forms\Components\Textarea::make('text')
                                                ->required()
                                                ->afterStateUpdated(function (Get $get, Set $set, $component) {
                                                    $componentStatePath = $component->getStatePath();
                                                    $componentUUID = explode('.', $componentStatePath)[2];
                                                    $answers = $get('../../answers');
                                                    $alphabet = range('a', 'z');
                                                    $optionLetter = $alphabet[array_search($componentUUID, array_keys($answers))];
                                                    $set('option', $optionLetter);
                                                })
                                                ->live(),
                                        ]),
                                Forms\Components\Select::make('correct_answer')
                                    ->disabled()
                                    ->formatStateUsing(function (Get $get) {
                                        return QuizQuestions::where('id', $get('quiz_question_id'))->pluck('correct_answer')->filter();
                                    })
                                    ->options(function (Get $get) {
                                        $alphabet = range('a', 'z');
                                        $answers = $get('answers');
                                        $tmpOptions = array_slice($alphabet, 0, count($answers));
                                        return array_combine(array_values($tmpOptions), array_values($tmpOptions));
                                    })
                                    ->native(false)
                                    ->columnSpan(1),
                                Forms\Components\Textarea::make('explanation')
                                    ->disabled()
                                    ->formatStateUsing(function (Get $get) {
                                        return QuizQuestions::where('id', $get('quiz_question_id'))->pluck('explanation')->filter();
                                    })
                                    ->columnSpan(5),
                            ]),
                        Forms\Components\Section::make()
                            ->columns(2)
                            ->schema([
                                DatePicker::make('date')
                                    ->columnSpan(1)
                                    ->default(date('Y-m-d')),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quizQuestion.question')
                    ->label(__('Question'))
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->lineClamp(2),
                Tables\Columns\TextColumn::make('quizQuestion.locale')
                    ->label(__('Locale'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('quizQuestion.region.name')
                    ->label(__('Region'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date')
                    ->label(__('Publish Date'))
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(function (Quizzes $quizzes) {
                        return Carbon::make($quizzes->date)->toDateString();
                    }),
            ])
            ->defaultSort('date', 'desc')
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
            ResponsesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuizzes::route('/'),
            'create' => Pages\CreateQuizzes::route('/create'),
            'edit' => Pages\EditQuizzes::route('/{record}/edit'),
        ];
    }
}
