<?php

namespace App\Filament\Resources\Components;

use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Get;
use Filament\Forms\Set;

class QuizQuestionCreatorAndEditor
{
    // QuizAnswer 'options' field gets ComponentStatePath, which varies if called as an action
    public static function getModal($isAction = false): array
    {
        return [
            Section::make()
                ->columns(6)
                ->schema([
                    Forms\Components\Textarea::make('question')
                        ->required()
                        ->columnSpan(6),
                    Forms\Components\Toggle::make('is_filler')
                        ->columnSpan(1),
                    Forms\Components\Repeater::make('answers')
                        ->columnSpanFull()
                        ->grid(2)
                        ->relationship('answers')
                        ->defaultItems(2)
                        ->minItems(2)
                        // full alphabet
                        ->maxItems(26)
                        ->addActionLabel('Add Answer')
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
                                Forms\Components\MarkdownEditor::make('text')
                                    ->required()
                                    ->afterStateUpdated(function (Get $get, Set $set, $component) use ($isAction) {
                                        $componentStatePath = $component->getStatePath();
                                        $componentUUID = explode('.', $componentStatePath)[$isAction ? 3 : 2];
                                        $answers = $get('../../answers');
                                        $alphabet = range('a', 'z');
                                        $optionLetter = $alphabet[array_search($componentUUID, array_keys($answers))];
                                        $set('option', $optionLetter);
                                    })
                                    ->live(),
                            ]),
                    Forms\Components\Select::make('correct_answer')
                        ->required()
                        ->options(function (Get $get) {
                            $alphabet = range('a', 'z');
                            $answers = $get('answers');
                            $tmpOptions = array_slice($alphabet, 0, count($answers));
                            return array_combine(array_values($tmpOptions), array_values($tmpOptions));
                        })
                        ->native(false)
                        ->columnSpan(1),
                    Forms\Components\MarkdownEditor::make('explanation')
                        ->columnSpanFull(),
                    LocaleSelector::getLocaleSelector(1),
                    RegionsSelector::getRegionsSelector(3),
                ]),
        ];
    }
}
