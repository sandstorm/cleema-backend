<?php

namespace Tests\Feature;

use App\Models\Files;
use App\Models\Trophies;
use App\Models\UpUsers;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertTrue;

uses(RefreshDatabase::class);

describe('Trophies API Tests', function () {
    beforeEach(function () {
    });

    it("fetches user's trophies", function () {
        $image = Files::factory()->create();
        $user = UpUsers::factory()->create(['uuid' => ApiTestHelper::$userUuid]);

        $accountAgeTrophy = Trophies::factory()->create();
        $accountAgeTrophy->image()->associate($image)->save();
        $user->trophies()->attach($accountAgeTrophy);

        $quizAnswersTrophy = Trophies::factory()->create(['kind' => 'quiz-correct-answers']);
        $quizAnswersTrophy->image()->associate($image)->save();
        $user->trophies()->attach($quizAnswersTrophy);

        $challengeParticipationTrophy = Trophies::factory()->create(['kind' => 'challenge-participation']);
        $challengeParticipationTrophy->image()->associate($image)->save();
        $user->trophies()->attach($challengeParticipationTrophy);

        $response = ApiTestHelper::makeGetRequest('/api/trophies/me');
        $response->assertStatus(200);

        $responseContent = json_decode($response->content(), true)['data'];

        assertCount($user->trophies()->count(), $responseContent);

        foreach ($responseContent as $responseTrophy) {
            switch ($responseTrophy['trophy']['kind']) {
                default:
                    $trophy = $accountAgeTrophy;
                    break;
                case 'quiz-correct-answers':
                    $trophy = $quizAnswersTrophy;
                    break;
                case 'challenge-participation':
                    $trophy = $challengeParticipationTrophy;
                    break;
            }

            assertEquals($trophy->kind, $responseTrophy['trophy']['kind']);
            assertEquals($trophy->amount, $responseTrophy['trophy']['amount']);
            assertEquals($trophy->title, $responseTrophy['trophy']['title']);
            assertEquals($trophy->uuid, $responseTrophy['trophy']['uuid']);
            assertEquals($trophy->image()->first()->name, $responseTrophy['trophy']['image']['name']);
            assertEquals('storage/' . $trophy->image()->first()->url, $responseTrophy['trophy']['image']['url']);
        }
    });


    it('fetches users new trophies', function () {
        $user = UpUsers::factory()->create(['uuid' => ApiTestHelper::$userUuid]);

        $notifiedTrophies = Trophies::factory(3)->create();
        $newTrophies = Trophies::factory(2)->create();

        foreach ($notifiedTrophies as $trophy) {
            $trophy->upUsers()->attach($user, ['notified' => true]);
        }
        foreach ($newTrophies as $trophy) {
            $trophy->upUsers()->attach($user, ['notified' => false]);
        }

        $response = ApiTestHelper::makeGetRequest('/api/trophies/me/new');
        $response->assertStatus(200);

        $responseContent = json_decode($response->content(), true)['data'];

        assertCount($newTrophies->count(), $responseContent);

        foreach ($responseContent as $trophy) {
            $arraySearchResult = array_search($trophy['trophy']['uuid'], $newTrophies->pluck('uuid')->toArray());
            assertTrue($arraySearchResult >= 0 && $arraySearchResult !== false);
        }
    });


    it('fetches one trophy', function () {
        $image = Files::factory()->create();
        $user = UpUsers::factory()->create(['uuid' => ApiTestHelper::$userUuid]);
        $trophy = Trophies::factory()->create();
        $trophy->image()->associate($image)->save();
        $trophy->upUsers()->attach($user);

        $response = ApiTestHelper::makeGetRequest('/api/trophies/' . $trophy->uuid);
        $response->assertStatus(200);

        $responseContent = json_decode($response->content(), true)['data'];

        assertEquals($trophy->kind, $responseContent['trophy']['kind']);
        assertEquals($trophy->title, $responseContent['trophy']['title']);
        assertEquals($trophy->locale, $responseContent['trophy']['locale']);
        assertEquals($trophy->uuid, $responseContent['trophy']['uuid']);
        assertEquals('storage/' . $trophy->image()->first()->url, $responseContent['trophy']['image']['url']);
        assertEquals($trophy->image()->first()->name, $responseContent['trophy']['image']['name']);
    });

});
