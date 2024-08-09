<?php

namespace Tests\Feature;

use App\Models\Regions;
use App\Models\UpUsers;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertModelExists;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertTrue;

uses(RefreshDatabase::class);

describe('Authentication API Tests', function () {
    beforeEach(function () {
    });

    it('registers a user', function () {
        $region = Regions::factory()->create();
        $user = UpUsers::factory()->make(['uuid' => ApiTestHelper::$userUuid]);

        $response = ApiTestHelper::makePostRequest('api/auth/local/register', [
            'password' => $user->password,
            'username' => $user->username,
            'email' => $user->email,
            'acceptsSurveys' => true,
            'region' => [
                'uuid' => $region->uuid,
            ],
        ]);
        $response->assertStatus(200);

        $responseContent = json_decode($response->content(), true);
        assertTrue(!empty($responseContent['user']));

        assertDatabaseHas('up_users', [
            'password' => $user->password,
            'username' => $user->username,
            'email' => $user->email,
            'accepts_surveys' => true,
            'region_id' => $region->id,
        ]);

        assertModelExists(UpUsers::query()->where([
            'password' => $user->password,
            'username' => $user->username,
            'email' => $user->email,
            'accepts_surveys' => true,
            'region_id' => $region->id,
        ])->first());
    });


    it("doesn't register taken username", function () {
        $region = Regions::factory()->create();
        UpUsers::factory()->create(['username' => 'TestUser']);
        $user = UpUsers::factory()->make([
            'uuid' => ApiTestHelper::$userUuid,
            'username' => 'TestUser'
        ]);

        $response = ApiTestHelper::makePostRequest('api/auth/local/register', [
            'password' => $user->password,
            'username' => $user->username,
            'email' => $user->email,
            'acceptsSurveys' => true,
            'region' => [
                'uuid' => $region->uuid,
            ],
        ]);
        $response->assertBadRequest();

        $responseContent = json_decode($response->content(), true);
        assertEquals('Username already taken.', $responseContent['error']['message']);
    });


    it("doesn't register taken email", function () {
        $region = Regions::factory()->create();
        UpUsers::factory()->create(['email' => 'testuser@test.de']);
        $user = UpUsers::factory()->make([
            'uuid' => ApiTestHelper::$userUuid,
            'email' => 'testuser@test.de'
        ]);

        $response = ApiTestHelper::makePostRequest('api/auth/local/register', [
            'password' => $user->password,
            'username' => $user->username,
            'email' => $user->email,
            'acceptsSurveys' => true,
            'region' => [
                'uuid' => $region->uuid,
            ],
        ]);
        $response->assertBadRequest();

        $responseContent = json_decode($response->content(), true);
        assertEquals('Email already taken.', $responseContent['error']['message']);
    });


    it('authenticates user', function () {
        $user = UpUsers::factory()->create([
            'uuid' => ApiTestHelper::$userUuid,
            'password' => 'password',
        ]);

        $response = ApiTestHelper::makePostRequest('api/auth/local/', [
            'identifier' => $user->username,
            'password' => 'password',
        ]);
        $response->assertStatus(200);

        $responseContent = json_decode($response->content(), true);

        assertTrue(!empty($responseContent['user']));
        assertEquals($user->email, $responseContent['user']['email']);
        assertEquals($user->username, $responseContent['user']['username']);
        assertEquals($user->uuid, $responseContent['user']['uuid']);
    });


    it("doesn't authenticate unconfirmed user", function () {
        $user = UpUsers::factory()->create([
            'uuid' => ApiTestHelper::$userUuid,
            'password' => 'password',
            'confirmed' => false,
        ]);

        $response = ApiTestHelper::makePostRequest('api/auth/local/', [
            'identifier' => $user->username,
            'password' => 'password',
        ]);
        $response->assertBadRequest();

        $responseContent = json_decode($response->content(), true);
        assertEquals('Your account email is not confirmed', $responseContent['error']['message']);
    });

});
