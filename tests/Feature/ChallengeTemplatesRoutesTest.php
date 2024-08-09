<?php

namespace Tests\Feature;

use App\Models\ChallengeTemplates;
use App\Models\Files;
use App\Models\Partners;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNull;
use function PHPUnit\Framework\assertTrue;

uses(RefreshDatabase::class);

describe('ChallengeTemplates API Tests', function () {
    beforeEach(function () {
    });

    it('fetches ChallengeTemplates', function () {
        $partner = Partners::factory()->create();
        $image = Files::factory()->create();
        $challengeTemplates = ChallengeTemplates::factory(7)->create();

        foreach ($challengeTemplates as $index => $template) {
            assert($template instanceof ChallengeTemplates);
            if($index % 2 == 0) {
                $template->image()->associate($image)->save();
            } else {
                $template->partner()->associate($partner)->save();
            }
        }

        $response = ApiTestHelper::makeGetRequest('/api/challenge-templates');
        $response->assertStatus(200);

        $responseContent = json_decode($response->content(), true)['data'];

        assertCount($challengeTemplates->count(), $responseContent);

        foreach ($responseContent as $responseTemplate) {
            $arraySearchResult = array_search($responseTemplate['title'], $challengeTemplates->pluck('title')->toArray());
            assertTrue($arraySearchResult >= 0 && $arraySearchResult !== false);
            $template = $challengeTemplates[$arraySearchResult];
            assertEquals($template->title, $responseTemplate['title']);
            assertEquals($template->description, $responseTemplate['description']);
            assertEquals($template->interval, $responseTemplate['interval']);
            assertEquals($template->is_public, $responseTemplate['isPublic']);
            assertEquals($template->kind, $responseTemplate['kind']);
            assertEquals($template->goal_type, $responseTemplate['goalType']);
            assertEquals($template->teaser_text, $responseTemplate['teaserText']);
            assertEquals($template->goal_measurement, $responseTemplate['goalMeasurement']);
            assertEquals($template->goal_steps, $responseTemplate['goalSteps']);

            $templatePartner = $template->partner()->first();
            if ($templatePartner != null) {
                assertEquals($templatePartner->uuid, $responseTemplate['partner']['uuid']);
                assertEquals($templatePartner->title, $responseTemplate['partner']['title']);
                assertEquals($templatePartner->url, $responseTemplate['partner']['url']);
            } else {
                assertNull($responseTemplate['partner']);
            }

            $templateImage = $template->image()->first();
            if ($templateImage != null) {
                assertEquals($templateImage->uuid, $responseTemplate['image']['uuid']);
                assertEquals($templateImage->name, $responseTemplate['image']['image']['name']);
                assertEquals('storage/' . $templateImage->url, $responseTemplate['image']['image']['url']);
            } else {
                assertNull($responseTemplate['image']);
            }
        }
    });

});
