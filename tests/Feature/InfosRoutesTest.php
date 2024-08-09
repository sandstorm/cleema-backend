<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\assertDatabaseHas;
use function PHPUnit\Framework\assertEquals;

uses(RefreshDatabase::class);

describe('Infos API Tests', function () {
    beforeEach(function () {
    });

    it('get about', function () {
        $about = fake()->text();

        \DB::table('abouts')->insert([
            'content' => $about
        ]);

        assertDatabaseHas('abouts', [
            'content' => $about
        ]);

        $response = ApiTestHelper::makeGetRequest('/api/about');
        $response->assertStatus(200);

        $responseContent = json_decode($response->content(), true)['data'];

        assertEquals($about, $responseContent['content']);
    });


    it('get privacy policy', function () {
        $privacyPolicy = fake()->text();

        \DB::table('privacy_policies')->insert([
            'content' => $privacyPolicy
        ]);

        assertDatabaseHas('privacy_policies', [
            'content' => $privacyPolicy
        ]);

        $response = ApiTestHelper::makeGetRequest('/api/privacy-policy');
        $response->assertStatus(200);

        $responseContent = json_decode($response->content(), true)['data'];

        assertEquals($privacyPolicy, $responseContent['content']);
    });


    it('get legal notice', function () {
        $legalNotice = fake()->text();

        \DB::table('legal_notices')->insert([
            'content' => $legalNotice
        ]);

        assertDatabaseHas('legal_notices', [
            'content' => $legalNotice
        ]);

        $response = ApiTestHelper::makeGetRequest('/api/legal-notice');
        $response->assertStatus(200);

        $responseContent = json_decode($response->content(), true)['data'];

        assertEquals($legalNotice, $responseContent['content']);
    });


    it('get partnership', function () {
        $partnership = fake()->text();

        \DB::table('partnerships')->insert([
            'content' => $partnership
        ]);

        assertDatabaseHas('partnerships', [
            'content' => $partnership
        ]);

        $response = ApiTestHelper::makeGetRequest('/api/partnership');
        $response->assertStatus(200);

        $responseContent = json_decode($response->content(), true)['data'];

        assertEquals($partnership, $responseContent['content']);
    });

});
