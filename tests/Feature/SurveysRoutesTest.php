<?php

namespace Tests\Feature;

use App\Models\Surveys;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEquals;

uses(RefreshDatabase::class);

describe('Surveys API Tests', function () {
    beforeEach(function () {
    });


    it('fetches surveys', function () {
        $surveys = Surveys::factory(2)->create();

        $response = ApiTestHelper::makeGetRequest('/api/surveys');
        $response->assertStatus(200);

        $responseContent = json_decode($response->content(), true)['data'];

        assertCount(2, $responseContent);
        foreach ($responseContent as $index => $responseSurvey) {
            $survey = $surveys[$index];
            assertEquals($survey->title, $responseSurvey['title']);
            assertEquals($survey->description, $responseSurvey['description']);
            assertEquals($survey->survey_url, $responseSurvey['surveyUrl']);
            assertEquals($survey->evaluation_url, $responseSurvey['evaluationUrl']);
            assertEquals($survey->target, $responseSurvey['target']);
            assertEquals($survey->trophy_processed, $responseSurvey['trophyProcessed']);
            assertEquals($survey->uuid, $responseSurvey['uuid']);
            assertEquals($survey->finished, $responseSurvey['finished']);
        }
    });

});
