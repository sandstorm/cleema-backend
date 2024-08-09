<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Challenges;
use App\Models\Files;
use App\Models\JoinedChallenges;
use App\Models\JoinedChallengesAnswers;
use App\Models\Regions;
use App\Models\UpUsers;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\assertModelExists;
use function Pest\Laravel\assertModelMissing;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEmpty;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertIsArray;
use function PHPUnit\Framework\assertIsInt;
use function PHPUnit\Framework\assertIsString;
use function PHPUnit\Framework\assertNotEmpty;
use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertTrue;

uses(RefreshDatabase::class);

describe('Challenges API Tests', function () {
    beforeEach(function () {
    });


    it('fetches challenges correctly', function () {
        $region = Regions::factory()->create();
        $regionalChallenges = Challenges::factory(3)->create();
        foreach ($regionalChallenges as $challenge) {
            $challenge->region()->associate($region)->save();
        }
        Challenges::factory(5)->create();

        $response = ApiTestHelper::makeGetRequest(
            '/api/challenges/?filters%5Bregion%5D%5Buuid%5D%5B$eq%5D=' . $region->uuid . '&filters%5Bkind%5D%5B$in%5D=partner,collective'
        );
        $response->assertStatus(200);

        $responseContent = json_decode($response->content(), true)['data'];

        assertCount($regionalChallenges->count(), $responseContent);

        foreach ($responseContent as $index => $challenge) {
            $challengeFactory = $regionalChallenges->where('uuid', $challenge['uuid'])->first();
            assertNotNull($challengeFactory);

            assertEquals($challenge['uuid'], $challengeFactory->uuid);
            assertEquals($challenge['title'], $challengeFactory->title);
            assertEquals($challenge['description'], $challengeFactory->description);
            assertEquals($challenge['interval'], $challengeFactory->interval);
            assertEquals($challenge['isPublic'], $challengeFactory->isPublic);
            assertEquals($challenge['kind'], $challengeFactory->kind);
            assertEquals($challenge['locale'], $challengeFactory->locale);
            assertEquals($challenge['goalType'], $challengeFactory->goal_type);
            assertEquals($challenge['teaserText'], $challengeFactory->teaser_text);
            assertEquals($challenge['region']['name'], $region->name);
            assertEquals($challenge['region']['uuid'], $region->uuid);
            assertEquals($challenge['collectiveGoalAmount'], $challengeFactory->collective_goal_amount ?? 0);
            assertEquals($challenge['collectiveProgress'], $challengeFactory->collective_progress ?? 0);

            assertEquals(Carbon::make($challengeFactory->start_date), Carbon::make($challenge['startDate']));
            assertEquals(Carbon::make($challengeFactory->end_date), Carbon::make($challenge['endDate']));
            assertEquals(Carbon::make($challengeFactory->published_at), Carbon::make($challenge['publishedAt']));
            assertEquals(Carbon::make($challengeFactory->created_at), Carbon::make($challenge['createdAt']));
            assertEquals(Carbon::make($challengeFactory->updated_at), Carbon::make($challenge['updatedAt']));

            if (!empty($responseContent[$index + 1])) {
                assertTrue(Carbon::make($challenge['startDate'])->format('Y-m-d H:i:s') >= Carbon::make($responseContent[$index + 1]['startDate'])->format('Y-m-d H:i:s'));
            }
        }


    });


    it('fetches collective Challenges correctly', function () {
        $region = Regions::factory()->create();
        $collectiveChallenges = Challenges::factory(3)->create(['kind' => 'collective']);
        foreach ($collectiveChallenges as $index => $challenge) {
            $challenge->region()->associate($region)->save();
            $challenge->collective_goal_amount = $index * 100 + 10;
            $challenge->save();

            $joinedChallenge = JoinedChallenges::factory()->create();
            $joinedChallenge->challenge()->associate($challenge)->save();

            $answers = JoinedChallengesAnswers::factory($index + 1)->create()->concat(
                JoinedChallengesAnswers::factory($index + 1)->create(['answer' => 'failed'])
            );

            foreach ($answers as $answer) {
                $answer->joinedChallenge()->associate($joinedChallenge)->save();
            }
        }

        $response = ApiTestHelper::makeGetRequest(
            '/api/challenges/?filters%5Bregion%5D%5Buuid%5D%5B$eq%5D=' . $region->uuid . '&filters%5Bkind%5D%5B$in%5D=partner,collective'
        );
        $response->assertStatus(200);

        $responseContent = json_decode($response->content(), true)['data'];
        assertCount(3, $responseContent);

        foreach ($responseContent as $responseChallenge) {
            $challenge = Challenges::where('uuid', $responseChallenge['uuid'])->first();

            $collectiveProgress = 0;
            $joinedChallenges = $challenge->joinedChallenges()->get();

            foreach ($joinedChallenges as $jc) {
                $answers = $jc->answers()->where('answer', '=', 'succeeded')->get();
                $collectiveProgress += count($answers);
            }

            assertEquals($collectiveProgress, $responseChallenge['collectiveProgress']);
            assertEquals($challenge->collective_goal_amount, $responseChallenge['collectiveGoalAmount']);
        }
    });


    it('fetches one challenge', function () {
        $image = Files::factory()->create();
        $region = Regions::factory()->create();
        $challenge = Challenges::factory()->create();
        $challenge->region()->associate($region)->save();
        $challenge->image()->associate($image)->save();

        $response = ApiTestHelper::makeGetRequest(
            '/api/challenges/' . $challenge->uuid
        );
        $response->assertStatus(200);

        $responseContent = json_decode($response->content(), true)['data'];

        assertEquals($responseContent['uuid'], $challenge->uuid);
        assertEquals($responseContent['title'], $challenge->title);
        assertEquals($responseContent['description'], $challenge->description);
        assertEquals($responseContent['interval'], $challenge->interval);
        assertEquals($responseContent['isPublic'], $challenge->isPublic);
        assertEquals($responseContent['kind'], $challenge->kind);
        assertEquals($responseContent['locale'], $challenge->locale);
        assertEquals($responseContent['goalType'], $challenge->goal_type);
        assertEquals($responseContent['teaserText'], $challenge->teaser_text);
        assertEquals($responseContent['region']['name'], $region->name);
        assertEquals($responseContent['region']['uuid'], $region->uuid);
        assertEquals($responseContent['collectiveGoalAmount'], $challenge->collective_goal_amount ?? 0);
        assertEquals($responseContent['collectiveProgress'], $challenge->collective_progress ?? 0);
        assertEquals($responseContent['image']['uuid'], $challenge->image()->first()->uuid);
        assertEquals($responseContent['image']['image']['url'], 'storage/' . $challenge->image()->first()->url);
    });


    it('fetches JoinedChallenges', function () {
        $user = UpUsers::factory()->create([
            'uuid' => ApiTestHelper::$userUuid,
        ]);
        $challenges = Challenges::factory(4)->create();
        foreach ($challenges as $index => $challenge) {
            $joinedChallenges = JoinedChallenges::factory($index + 1)->create();

            foreach ($joinedChallenges as $jcIndex => $joinedChallenge) {
                $joinedChallenge->challenge()->associate($challenge)->save();
                $joinedChallenge->user()->associate(
                    $jcIndex == 0 ?
                        $user
                        : UpUsers::factory()->create())
                    ->save();

                $answers = JoinedChallengesAnswers::factory($index + 1)->create();
                foreach ($answers as $answer) {
                    $answer->joinedChallenge()->associate($joinedChallenge)->save();
                }
            }
        }

        $response = ApiTestHelper::makeGetRequest(
            '/api/challenges/?joined=true'
        );
        $response->assertStatus(200);

        $responseContent = json_decode($response->content(), true)['data'];
        assertCount($user->challengesJoined()->get()->count(), $responseContent);

        foreach ($responseContent as $challenge) {
            $isInUserChallengesJoined = array_search(
                $challenge['uuid'],
                $user->challengesJoined()->pluck('uuid')->toArray()
            );
            assertTrue($isInUserChallengesJoined >= 0 && $isInUserChallengesJoined !== false);

            assertNotEmpty($challenge['joinedChallenge']);
            assertNotEmpty($challenge['joinedChallenge']['answers']);
            foreach ($challenge['joinedChallenge']['answers'] as $joinedChallengeAnswer) {
                assertIsString($joinedChallengeAnswer['answer']);
                assertIsInt($joinedChallengeAnswer['dayIndex']);
            }
            $isUserJoined = false;
            foreach ($challenge['usersJoined'] as $joinedUser) {
                if ($joinedUser['uuid'] == ApiTestHelper::$userUuid && $joinedUser['uuid'] == $user->uuid) {
                    $isUserJoined = true;
                    break;
                }
            }
            assertTrue($isUserJoined);
        }
    });


    it('joins challenge', function () {
        $challenge = Challenges::factory()->create();

        $response = ApiTestHelper::makePatchRequest('api/challenges/' . $challenge->uuid . '/join');
        $response->assertStatus(200);

        $responseContent = json_decode($response->content(), true)['data'];

        assertEquals($responseContent['uuid'], $challenge->uuid);
        assertIsArray($responseContent['joinedChallenge']);
        assertIsArray($responseContent['usersJoined']);

        assertTrue(!empty($responseContent['usersJoined']));
        assertEquals($responseContent['usersJoined'][0]['uuid'], ApiTestHelper::$userUuid);

        assertModelExists(JoinedChallenges::where('challenge_id', $challenge->id)
            ->where('user_id', UpUsers::where('uuid', ApiTestHelper::$userUuid)->first()->id)
            ->first());
    });


    it('leaves challenge', function () {
        $challenge = Challenges::factory()->create();
        $user = UpUsers::factory()->create(['uuid' => ApiTestHelper::$userUuid]);
        $user->challengesJoined()->attach($challenge->id);
        $joinedChallenge = JoinedChallenges::where('challenge_id', $challenge->id)
            ->where('user_id', UpUsers::where('uuid', ApiTestHelper::$userUuid)->first()->id)
            ->first();

        assertModelExists($joinedChallenge);

        $response = ApiTestHelper::makePatchRequest('api/challenges/' . $challenge->uuid . '/leave');
        $response->assertStatus(200);

        $responseContent = json_decode($response->content(), true)['data'];

        assertModelMissing($joinedChallenge);

        assertEmpty($responseContent['usersJoined']);
        assertEmpty($responseContent['joinedChallenge']);

        assertCount(0, $user->joinedChallenges()->get());
        assertCount(0, $user->challengesJoined()->get());
        assertCount(0, $challenge->joinedUsers()->get());
    });


    it('answers challenge', function() {
        $challenge = Challenges::factory()->create();
        $user = UpUsers::factory()->create([ 'uuid' => ApiTestHelper::$userUuid ]);
        $user->challengesJoined()->attach($challenge->id);
        $joinedChallenge = $user->joinedChallenges()->where('challenge_id', $challenge->id)->first();

        assertModelExists($joinedChallenge);

        $response = ApiTestHelper::makePatchRequest(
            uri: 'api/challenges/' . $challenge->uuid . '/answer',
            parameters: [
                'answers' => [
                    [
                        'answer' => 'succeeded',
                        'dayIndex' => 1,
                    ],
                ]
            ]);
        $response->assertStatus(200);

        $responseContent = json_decode($response->content(), true)['data'];

        assertEquals($responseContent['uuid'], $challenge->uuid);

        assert($joinedChallenge instanceof JoinedChallenges);
        assertCount(1, $joinedChallenge->answers()->get());
        assertCount(1, $joinedChallenge->answers()->where('answer', 'succeeded')->get());

        $response = ApiTestHelper::makePatchRequest(
            uri: 'api/challenges/' . $challenge->uuid . '/answer',
            parameters: [
                'answers' => [
                    [
                        'answer' => 'failed',
                        'dayIndex' => 2,
                    ],
                ]
            ]);
        $response->assertStatus(200);

        $responseContent = json_decode($response->content(), true)['data'];

        assertCount($responseContent['collectiveProgress'], $joinedChallenge->answers()->where('answer', 'succeeded')->get());
        assertCount(2, $joinedChallenge->answers()->get());
        assertCount(1, $joinedChallenge->answers()->where('answer', 'succeeded')->get());
        assertCount(1, $joinedChallenge->answers()->where('answer', 'failed')->get());
    });

});
