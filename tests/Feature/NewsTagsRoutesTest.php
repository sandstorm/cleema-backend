<?php

namespace Tests\Feature;

use App\Models\NewsTags;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertTrue;

uses(RefreshDatabase::class);

describe('NewsTags API Tests', function () {
    beforeEach(function () {
    });

    it('fetches NewsTags', function () {
        $newsTags = NewsTags::factory(7)->create();

        $response = ApiTestHelper::makeGetRequest('/api/news-tags');
        $response->assertStatus(200);

        $responseContent = json_decode($response->content(), true)['data'];

        foreach ($responseContent as $responseTag) {
            $arraySearchResult = array_search($responseTag['uuid'], $newsTags->pluck('uuid')->toArray());
            assertTrue($arraySearchResult >= 0 && $arraySearchResult !== false);
            $tag = $newsTags[$arraySearchResult];
            assertEquals($tag->uuid, $responseTag['uuid']);
            assertEquals($tag->value, $responseTag['value']);
            assertEquals($tag->locale, $responseTag['locale']);
        }
    });

});
