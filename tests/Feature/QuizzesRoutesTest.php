<?php

namespace Tests\Feature;

use App\Models\QuizAnswers;
use App\Models\QuizQuestions;
use App\Models\Regions;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertModelExists;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertIsArray;
use function PHPUnit\Framework\assertTrue;

uses(RefreshDatabase::class);

describe('Quizzes API Tests', function () {

    beforeEach(function () {
    });


    it('fetches correct supraregional quiz when no region given', function () {
        $region = Regions::factory()->create([
            'is_supraregional' => true,
        ]);

        $currentQuiz = ApiTestHelperQuizzes::createTestQuizzes(region: $region);

        $response = ApiTestHelper::makeGetRequest('http://localhost/api/quizzes/current');

        $response->assertStatus(200);

        $responseContent = json_decode($response->content(), true)['data'];

        assertEquals(ApiTestHelperQuizzes::$todaysQuestion, $responseContent['question']);
        assertEquals(ApiTestHelperQuizzes::$todaysCorrectAnswer, $responseContent['correctAnswer']);
        assertEquals(ApiTestHelperQuizzes::$todaysExplanation, $responseContent['explanation']);
        assertEquals(Carbon::now()->toDateString(), Carbon::make($responseContent['date'])->toDateString());
        assertEquals($currentQuiz->uuid, $responseContent['uuid']);
        assertEquals('de-DE', $responseContent['locale']);
        assertCount(2, $responseContent['answers']);
        assertEquals('a', $responseContent['answers'][0]['option']);
        assertEquals(ApiTestHelperQuizzes::$todaysAnswerA, $responseContent['answers'][0]['text']);
        assertEquals('b', $responseContent['answers'][1]['option']);
        assertEquals(ApiTestHelperQuizzes::$todaysAnswerB, $responseContent['answers'][1]['text']);
    });


    it('fetches correct regional quiz', function () {
        $region = Regions::factory()->create();

        ApiTestHelperQuizzes::createTestQuizzes(createRightQuiz: false);

        $currentQuiz = ApiTestHelperQuizzes::createTestQuizzes(region: $region);

        $currentQuizQuestion = $currentQuiz->quizQuestion()->first();

        assertModelExists($currentQuizQuestion);
        assertTrue($currentQuizQuestion->region()->first()->uuid === $region->uuid);

        $response = ApiTestHelper::makeGetRequest(
            'http://localhost/api/quizzes/current?filters%5B$or%5D%5B1%5D%5Bregion%5D%5Buuid%5D%5B$eq%5D=' . $region->uuid
        );

        $response->assertStatus(200);

        $responseContent = json_decode($response->content(), true)['data'];

        assertEquals(ApiTestHelperQuizzes::$todaysQuestion, $responseContent['question']);
        assertEquals(ApiTestHelperQuizzes::$todaysCorrectAnswer, $responseContent['correctAnswer']);
        assertEquals(ApiTestHelperQuizzes::$todaysExplanation, $responseContent['explanation']);
        assertEquals(Carbon::now()->toDateString(), Carbon::make($responseContent['date'])->toDateString());
        assertEquals($currentQuiz->uuid, $responseContent['uuid']);
        assertEquals('de-DE', $responseContent['locale']);
        assertCount(2, $responseContent['answers']);
        assertEquals('a', $responseContent['answers'][0]['option']);
        assertEquals(ApiTestHelperQuizzes::$todaysAnswerA, $responseContent['answers'][0]['text']);
        assertEquals('b', $responseContent['answers'][1]['option']);
        assertEquals(ApiTestHelperQuizzes::$todaysAnswerB, $responseContent['answers'][1]['text']);
    });


    it('creates Filler Quiz when no regional quiz for today found', function () {
        $region = Regions::factory()->create();
        $supraregionalRegion = Regions::factory()->create(['is_supraregional' => true]);
        $quizQuestion = QuizQuestions::factory()->create(['is_filler' => true]);
        $quizQuestion->region()->associate($supraregionalRegion)->save();

        for ($i = 0; $i < 2; $i++) {
            $quizAnswer = QuizAnswers::factory()->create([
                'option' => $i == 0 ? 'a' : 'b',
            ]);
            $quizAnswer->quizQuestion()->associate($quizQuestion)->save();
        }

        $response = ApiTestHelper::makeGetRequest(
            'http://localhost/api/quizzes/current?filters%5B$or%5D%5B1%5D%5Bregion%5D%5Buuid%5D%5B$eq%5D=' . $region->uuid
        );

        $response->assertStatus(200);

        $responseContent = json_decode($response->content(), true)['data'];

        assertDatabaseHas('quizzes', [
            'quiz_question_id' => $quizQuestion->id
        ]);

        $answers = $quizQuestion->answers()->get();
        assertEquals($quizQuestion->question, $responseContent['question']);
        assertEquals($quizQuestion->correct_answer, $responseContent['correctAnswer']);
        assertEquals(Carbon::now()->toDateString(), Carbon::make($responseContent['date'])->toDateString());
        assertIsArray($responseContent['answers']);
        assertCount($answers->count(), $responseContent['answers']);
        assertEquals($answers[0]->option, $responseContent['answers']['0']['option']);
        assertEquals($answers[0]->text, $responseContent['answers']['0']['text']);
        assertEquals($answers[1]->option, $responseContent['answers']['1']['option']);
        assertEquals($answers[1]->text, $responseContent['answers']['1']['text']);
    });


    it('creates Filler Quiz when no supraregional quiz for today found', function () {
        $supraregionalRegion = Regions::factory()->create(['is_supraregional' => true]);
        $quizQuestion = QuizQuestions::factory()->create(['is_filler' => true]);
        $quizQuestion->region()->associate($supraregionalRegion)->save();

        for ($i = 0; $i < 2; $i++) {
            $quizAnswer = QuizAnswers::factory()->create([
                'option' => $i == 0 ? 'a' : 'b',
            ]);
            $quizAnswer->quizQuestion()->associate($quizQuestion)->save();
        }

        $response = ApiTestHelper::makeGetRequest('http://localhost/api/quizzes/current');

        $response->assertStatus(200);

        $responseContent = json_decode($response->content(), true)['data'];

        assertDatabaseHas('quizzes', [
            'quiz_question_id' => $quizQuestion->id
        ]);

        $answers = $quizQuestion->answers()->get();
        assertEquals($quizQuestion->question, $responseContent['question']);
        assertEquals($quizQuestion->correct_answer, $responseContent['correctAnswer']);
        assertEquals(Carbon::now()->toDateString(), Carbon::make($responseContent['date'])->toDateString());
        assertIsArray($responseContent['answers']);
        assertCount($answers->count(), $responseContent['answers']);
        assertEquals($answers[0]->option, $responseContent['answers']['0']['option']);
        assertEquals($answers[0]->text, $responseContent['answers']['0']['text']);
        assertEquals($answers[1]->option, $responseContent['answers']['1']['option']);
        assertEquals($answers[1]->text, $responseContent['answers']['1']['text']);
    });


    it('can answer a quiz', function () {
        $currentQuiz = ApiTestHelperQuizzes::createTestQuizzes(subDays: [0]);

        $response = ApiTestHelper::makePostRequest(
            uri: 'http://localhost/api/quiz-responses',
            parameters: [
                'data' => [
                    'answer' => 'a',
                    'quiz' => $currentQuiz->uuid,
                ]
            ],
        );

        $response->assertStatus(200);

        $responseContent = json_decode($response->content(), true)['data'];

        assertEquals('a', $responseContent['answer']);
        assertEquals(Carbon::now()->toDateString(), Carbon::make($responseContent['date'])->toDateString());
        assertEquals($currentQuiz->uuid, $responseContent['uuid']);

        $quizUser = $currentQuiz->responses()->first();
        assertModelExists($quizUser);

        assertDatabaseHas(
            'quiz_responses_v2',
            [
                'quiz_id' => $currentQuiz->id,
                'user_id' => $quizUser->id,
                'answer' => 'a',
            ]
        );

    });


});
