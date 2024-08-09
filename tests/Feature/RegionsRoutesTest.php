<?php

namespace Tests\Feature;

use App\Models\Regions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertTrue;

uses(RefreshDatabase::class);

describe('Regions API Tests', function () {
    beforeEach(function () {
    });

    it('fetches regions', function () {
        $regions = Regions::factory(7)->create();

        $response = ApiTestHelper::makeGetRequest('/api/regions');
        $response->assertStatus(200);

        $responseContent = json_decode($response->content(), true)['data'];

        assertCount($regions->count(), $responseContent);

        foreach ($responseContent as $responseRegion) {
            $arraySearchResult = array_search($responseRegion['uuid'], $regions->pluck('uuid')->toArray());
            assertTrue($arraySearchResult >= 0 && $arraySearchResult !== false);
            assertTrue($regions[$arraySearchResult]->is_public);
            assertEquals($regions[$arraySearchResult]->name, $responseRegion['name']);
        }
    });
});
