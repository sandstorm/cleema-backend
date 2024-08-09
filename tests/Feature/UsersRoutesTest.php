<?php

namespace Tests\Feature;

use App\Models\Files;
use App\Models\Regions;
use App\Models\UpUsers;
use App\Models\UserAvatars;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertTrue;

uses(RefreshDatabase::class);

describe('Users API Tests', function () {
    beforeEach(function () {
    });

    it('gets logged in user', function () {
        $region = Regions::factory()->create();
        $avatar = UserAvatars::factory()->create();
        $user = UpUsers::factory()->create(['uuid' => ApiTestHelper::$userUuid]);
        $user->region()->associate($region)->save();
        $user->avatar()->associate($avatar)->save();

        $response = ApiTestHelper::makeGetRequest('/api/users/me/');
        $response->assertStatus(200);

        $responseContent = json_decode($response->content(), true)['data'];

        assertCount(1, $responseContent);

        $userResponse = $responseContent['user'];

        assertEquals($user->uuid, $userResponse['uuid']);
        assertEquals($user->username, $userResponse['username']);
        assertEquals($user->email, $userResponse['email']);
        assertEquals($user->referral_code, $userResponse['referralCode']);
        assertEquals($user->uuid, $userResponse['uuid']);
        assertEquals($user->provider, $userResponse['provider']);
        assertEquals($user->confirmed, $userResponse['confirmed']);
        assertEquals($region->name, $userResponse['region']['name']);
        assertEquals($region->uuid, $userResponse['region']['uuid']);
        assertEquals($region->uuid, $userResponse['region']['uuid']);
        assertEquals($avatar->uuid, $userResponse['avatar']['uuid']);
        assertEquals(0, $responseContent['user']['follows']['followRequests']);
        assertEquals(0, $responseContent['user']['follows']['followers']);
        assertEquals(0, $responseContent['user']['follows']['following']);
        assertEquals(0, $responseContent['user']['follows']['followingPending']);
    });


    it('get users following', function () {
        $user = UpUsers::factory()->create(['uuid' => ApiTestHelper::$userUuid]);

        $users = UpUsers::factory(4)->create();
        foreach ($users as $u) {
            $user->follows()->attach($u);
            $user->followers()->attach($u);
        }

        $response = ApiTestHelper::makeGetRequest('/api/users/me/follows');
        $response->assertStatus(200);

        $responseContent = json_decode($response->content(), true)['data'];

        assertCount(2, $responseContent);

        assertCount($users->count(), $responseContent['followers']);
        assertCount($users->count(), $responseContent['following']);

        $usersUuids = $users->pluck('uuid')->toArray();
        foreach ($responseContent['followers'] as $u) {
            $arraySearchResult = array_search($u['uuid'], $usersUuids);
            assertTrue($arraySearchResult >= 0 && $arraySearchResult !== false);
        }
        foreach ($responseContent['following'] as $u) {
            $arraySearchResult = array_search($u['uuid'], $usersUuids);
            assertTrue($arraySearchResult >= 0 && $arraySearchResult !== false);
        }
    });


    it('follows another user', function () {
        $image = Files::factory()->create();
        $avatar = UserAvatars::factory()->create();
        $avatar->image()->associate($image)->save();

        $user = UpUsers::factory()->create(['uuid' => ApiTestHelper::$userUuid]);
        $user->avatar()->associate($avatar)->save();
        $friend = UpUsers::factory()->create();
        $friend->avatar()->associate($avatar)->save();

        assertDatabaseMissing('user_follows_v2', [
            'followed_user_id' => $user->id,
            'follows_user_id' => $friend->id,
        ]);
        assertDatabaseMissing('user_follows_v2', [
            'followed_user_id' => $friend->id,
            'follows_user_id' => $user->id,
        ]);

        $response = ApiTestHelper::makePostRequest('/api/users/me/follows', [
            'ref' => $friend->referral_code,
        ]);
        $response->assertStatus(200);

        $responseContent = json_decode($response->content(), true)['data'];

        assertEquals($friend->uuid, $responseContent['user']['uuid']);
        assertEquals($friend->username, $responseContent['user']['username']);
        assertEquals($avatar->uuid, $responseContent['user']['avatar']['uuid']);
        assertEquals($image->name, $responseContent['user']['avatar']['image']['name']);

        assertDatabaseHas('user_follows_v2', [
            'followed_user_id' => $user->id,
            'follows_user_id' => $friend->id,
        ]);
        assertDatabaseHas('user_follows_v2', [
            'followed_user_id' => $friend->id,
            'follows_user_id' => $user->id,
        ]);
    });


    it('unfollows another user', function () {
        $image = Files::factory()->create();
        $avatar = UserAvatars::factory()->create();
        $avatar->image()->associate($image)->save();

        $user = UpUsers::factory()->create(['uuid' => ApiTestHelper::$userUuid]);
        $user->avatar()->associate($avatar)->save();
        $friend = UpUsers::factory()->create();
        $friend->avatar()->associate($avatar)->save();
        $user->followers()->attach($friend);
        $user->follows()->attach($friend);

        assertDatabaseHas('user_follows_v2', [
            'followed_user_id' => $user->id,
            'follows_user_id' => $friend->id,
        ]);
        assertDatabaseHas('user_follows_v2', [
            'followed_user_id' => $friend->id,
            'follows_user_id' => $user->id,
        ]);

        $response = ApiTestHelper::makeDeleteRequest('/api/users/me/follows/' . $friend->uuid);
        $response->assertStatus(200);

        $responseContent = json_decode($response->content(), true)['data'];

        assertCount(0, $responseContent['followers']);
        assertCount(0, $responseContent['following']);

        assertDatabaseMissing('user_follows_v2', [
            'followed_user_id' => $user->id,
            'follows_user_id' => $friend->id,
        ]);
        assertDatabaseMissing('user_follows_v2', [
            'followed_user_id' => $friend->id,
            'follows_user_id' => $user->id,
        ]);
    });

});
