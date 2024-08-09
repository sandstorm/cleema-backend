<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\QuizAnswers;
use App\Models\QuizQuestions;
use App\Models\Quizzes;
use App\Models\Regions;
use Carbon\Carbon;

class ApiTestHelperQuizzes
{
    public static string $todaysQuestion = 'Is this the real life?';
    public static string $todaysExplanation = 'Is this just fantasy?';
    public static string $todaysCorrectAnswer = 'b';
    public static string $todaysAnswerA = 'Bohemian Rhapsody by Journey';
    public static string $todaysAnswerB = 'Bohemian Rhapsody by Queen';

    public static function createTestQuizzes(
        array $subDays = [-1, 0, 1],
        ?Regions $region = null,
        ?bool $createRightQuiz = true,
    ): ?Quizzes
    {
        if ($region === null) {
            $region = Regions::factory()->create([
                'is_supraregional' => true,
            ]);
        }

        $filteredSubDays = array_filter($subDays, function ($value) {
            return $value > -1;
        });

        $currentQuiz = null;

        $wrongQuestion = QuizQuestions::factory()->create([
            'question' => "Is this really today's quiz?",
            'correct_answer' => 'a',
            'explanation' => 'No, it is not.',
        ]);
        $wrongQuestion->region()->associate($region)->save();
        QuizAnswers::factory()->create([
            'option' => 'a',
            'text' => 'No',
        ])->quizQuestion()->associate($wrongQuestion)->save();

        QuizAnswers::factory()->create([
            'option' => 'b',
            'text' => 'Yes',
        ])->quizQuestion()->associate($wrongQuestion)->save();

        $currentQuizSubDays = min($filteredSubDays);

        foreach ($subDays as $index => $subDay) {
            $quizQuestion = null;
            if ($subDay == $currentQuizSubDays && $createRightQuiz) {
                $quizQuestion = QuizQuestions::factory()->create([
                    'question' => self::$todaysQuestion,
                    'correct_answer' => self::$todaysCorrectAnswer,
                    'explanation' => self::$todaysExplanation,
                    'locale' => 'de-DE'
                ]);
                $quizQuestion->region()->associate($region)->save();

                $quizAnswerA = QuizAnswers::factory()->create([
                    'option' => 'a',
                    'text' => self::$todaysAnswerA,
                ]);
                $quizAnswerA->quizQuestion()->associate($quizQuestion)->save();

                $quizAnswerB = QuizAnswers::factory()->create([
                    'option' => 'b',
                    'text' => self::$todaysAnswerB,
                ]);
                $quizAnswerB->quizQuestion()->associate($quizQuestion)->save();
            } else {
                $quizQuestion = $wrongQuestion;
            }

            $quiz = Quizzes::factory()->create([
                'date' => Carbon::now()->subDays($subDay)
            ]);
            $quiz->quizQuestion()->associate($quizQuestion)->save();

            if ($subDay == $currentQuizSubDays) {
                $currentQuiz = $quiz;
            }
        }

        if ($currentQuiz != null) {
            return $currentQuiz;
        } else {
            return null;
        }
    }
}
