<?php

namespace Tests\Feature;

use App\Models\Files;
use App\Models\NewsEntries;
use App\Models\Regions;
use App\Models\UpUsers;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertTrue;

uses(RefreshDatabase::class);

describe('News Entries API Tests', function () {

    beforeEach(function () {
    });


    it('fetches correct amount of news - fetch - /api/news-entries/', function () {
        NewsEntries::factory(7)->create([
            'published_at' => Carbon::now()->subDays(1)->format('Y-m-d H:i:s')
        ]);

        $response = ApiTestHelper::makeGetRequest('/api/news-entries/');
        $response->assertStatus(200);

        $responseContent = json_decode($response->content(), true)['data'];

        assertCount(count(NewsEntries::all()), $responseContent);
    });


    it('fetches correct news - fetch - /api/news-entries/', function () {
        $published_at = now()->subHour()->format('Y-m-d H:i:s');
        $newsEntry = NewsEntries::factory()->create([
            'title' => 'Cleema',
            'description' => 'This is a Cleema App test',
            'teaser' => 'Cleema App',
            'published_at' => $published_at
        ]);

        $response = ApiTestHelper::makeGetRequest('/api/news-entries/');
        $response->assertStatus(200);

        $responseContent = json_decode($response->content(), true)['data'];

        assertCount(count(NewsEntries::all()), $responseContent);

        assertEquals($responseContent[0]['uuid'], $newsEntry->uuid);
        assertEquals($responseContent[0]['title'], $newsEntry->title);
        assertEquals($responseContent[0]['description'], $newsEntry->description);
        assertEquals($responseContent[0]['teaser'], $newsEntry->teaser);
        assertEquals(Carbon::make($responseContent[0]['publishedAt'])->format('Y-m-d H:i:s'), $published_at);
        assertEquals($responseContent[0]['publishedAt'], Carbon::make($published_at)->format('Y-m-d\TH:i:s.000000\Z'));
    });


    it('fetches news correctly sorted by published_at - fetch - /api/news-entries/', function () {
        for ($i = 0; $i < 4; $i++) {
            NewsEntries::factory()->create([
                'published_at' => Carbon::now()->subDays($i)->format('Y-m-d H:i:s')
            ]);
            NewsEntries::factory()->create([
                'published_at' => Carbon::now()->subHours($i)->format('Y-m-d H:i:s')
            ]);
        }

        $response = ApiTestHelper::makeGetRequest('/api/news-entries/');
        $response->assertStatus(200);

        $responseContent = json_decode($response->content(), true)['data'];

        assertTrue($responseContent[0]['publishedAt'] > end($responseContent)['publishedAt']);
        for ($i = 1; $i < count($responseContent); $i++) {
            assertTrue($responseContent[$i-1]['publishedAt'] > $responseContent[$i]['publishedAt']);
        }
    });


    it('does not fetch future news - fetch - /api/news-entries/', function () {
        NewsEntries::factory(7)->create([
            'published_at' => Carbon::now()->subDay()->format('Y-m-d H:i:s')
        ]);

        $futureNewsEntry = NewsEntries::factory()->create([
            'title' => 'Future',
            'description' => "Shouldn't be in response",
            'teaser' => 'News from the future',
            'published_at' => Carbon::now()->addDay()->format('Y-m-d H:i:s')
        ]);

        $response = ApiTestHelper::makeGetRequest('/api/news-entries/');
        $response->assertStatus(200);

        $responseContent = json_decode($response->content(), true)['data'];

        assertFalse(count(NewsEntries::all()) == count($responseContent));
    });


    it('fetches news for region', function () {
        $region = Regions::factory()->create([
            'name' => 'Dresden'
        ]);

        $regionalNewsEntries = NewsEntries::factory(3)->create([
            'published_at' => Carbon::now()->subDay()
        ]);
        foreach ($regionalNewsEntries as $entry){
            $entry->region()->associate($region);
            $entry->save();
        }
        NewsEntries::factory(4)->create([
            'published_at' => Carbon::now()->subDay()
        ]);

            //== '/api/news-entries?filters=[$or][1][region][uuid][$eq]='
        $response = ApiTestHelper::makeGetRequest(
            uri: '/api/news-entries?filters%5B$or%5D%5B1%5D%5Bregion%5D%5Buuid%5D%5B$eq%5D='.$region->uuid.'&filters%5B$or%5D%5B2%5D%5Btype%5D%5B$eq%5D=tip'
        );
        $response->assertStatus(200);

        $responseContent = json_decode($response->content(), true)['data'];

        assertCount(3, $responseContent);
    });


    it('fetches one entry', function () {
        $newsEntry = NewsEntries::factory()->create();

        $region = Regions::factory()->create();
        $newsEntry->region()->associate($region)->save();

        $image = Files::factory()->create();
        $newsEntry->image()->associate($image)->save();

        $response = ApiTestHelper::makeGetRequest('/api/news-entries/'.$newsEntry->uuid);
        $response->assertStatus(200);

        $responseContent = json_decode($response->content(), true)['data'];

        expect($responseContent)
            ->title->toBe($newsEntry->title)
            ->description->toBe($newsEntry->description)
            ->locale->toBe($newsEntry->locale)
            ->uuid->toBe($newsEntry->uuid)
            ->teaser->toBe($newsEntry->teaser)
            ->type->toBe($newsEntry->type)
            ->region->name->toBe($region->name)
            ->region->uuid->toBe($region->uuid)
            ->image->name->toBe($image->name)
            ->image->hash->toBe($image->hash)
            // factory automatically adds 'storage/' to the url, although it does not when uploading a file
            ->image->url->toBe('storage/'.$image->url);

        assertEquals(Carbon::make($newsEntry->published_at), Carbon::make($responseContent['publishedAt']));
        assertEquals(Carbon::make($newsEntry->date), Carbon::make($responseContent['date']));
    });


    it('faves an entry', function () {
        $newsEntry = NewsEntries::factory()->create();

        $response = ApiTestHelper::makePatchRequest('api/news-entries/'.$newsEntry->uuid.'/fav');
        $response->assertStatus(200);

        $responseContent = json_decode($response->content(), true)['data'];

        assertEquals($newsEntry->uuid, $responseContent['uuid']);

        $user = UpUsers::where('uuid', ApiTestHelper::$userUuid)->first();

        assertCount(1, $newsEntry->usersFavorited()->get());
        assertEquals($user->uuid, $newsEntry->usersFavorited()->first()->uuid);
        assertEquals(true, $responseContent['isFaved']);

        assertDatabaseHas('up_users_favorited_news_links', [
            'user_id' => $user->id,
            'news_entry_id' => $newsEntry->id,
        ]);
    });


    it('unfaves an entry', function () {
        $newsEntry = NewsEntries::factory()->create();
        $user = UpUsers::factory()->create([
            'uuid' => ApiTestHelper::$userUuid
        ]);
        $newsEntry->usersFavorited()->attach($user);

        assertCount(1, $newsEntry->usersFavorited()->get());
        assertDatabaseHas('up_users_favorited_news_links', [
            'user_id' => $user->id,
            'news_entry_id' => $newsEntry->id,
        ]);

        $response = ApiTestHelper::makePatchRequest('api/news-entries/'.$newsEntry->uuid.'/unfav');
        $response->assertStatus(200);

        $responseContent = json_decode($response->content(), true)['data'];

        assertCount(0, $newsEntry->usersFavorited()->get());
        assertDatabaseMissing('up_users_favorited_news_links', [
            'user_id' => $user->id,
            'news_entry_id' => $newsEntry->id,
        ]);
    });

});
