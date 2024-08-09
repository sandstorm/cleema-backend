<?php

namespace Tests\Feature;

use App\Models\Files;
use App\Models\NewsEntries;
use App\Models\Partners;
use App\Models\Projects;
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

describe('Projects API Tests', function () {

    beforeEach(function () {
    });


    it('fetches projects correctly', function () {
        $user = UpUsers::factory()->create();
        $region = Regions::factory()->create();

        $runningProjects = Projects::factory(3)->create([
            'phase' => 'running',
        ]);
        $previewProjects = Projects::factory(4)->create([
            'phase' => 'preview',
        ]);
        $finishedProjects = Projects::factory(5)->create([
            'phase' => 'finished',
        ]);

        foreach (($runningProjects->concat($previewProjects))->concat($finishedProjects) as $project) {
            $project->region()->associate($region)->save();
        }

        $response = ApiTestHelper::makeGetRequest('/api/projects?filters%5Bregion%5D%5Buuid%5D%5B$eq%5D=' . $region->uuid);
        $response->assertStatus(200);

        $responseContent = json_decode($response->content(), true)['data'];

        $notFinishedProjects = Projects::where('region_id', $region->id)->where('phase', '!=', 'finished')->get();
        assertCount(count($notFinishedProjects), $responseContent);

        foreach ($responseContent as $project) {
            $arraySearchResult = array_search($project['uuid'], $notFinishedProjects->pluck('uuid')->toArray());
            assertTrue($arraySearchResult >= 0 && $arraySearchResult !== false);
        }
    });


    it('fetches one project', function () {
        $image = Files::factory()->create();
        $region = Regions::factory()->create();
        $partner = Partners::factory()->create();
        $project = Projects::factory()->create();
        $project->region()->associate($region)->save();
        $project->partner()->associate($partner)->save();
        $project->image()->associate($image)->save();

        $response = ApiTestHelper::makeGetRequest('/api/projects/' . $project->uuid);
        $response->assertStatus(200);

        $responseContent = json_decode($response->content(), true)['data'];

        assertEquals($responseContent['title'], $project->title);
        assertEquals($responseContent['summary'], $project->summary);
        assertEquals($responseContent['description'], $project->description);
        assertEquals($responseContent['locale'], $project->locale);
        assertEquals($responseContent['goalType'], $project->goal_type);
        assertEquals($responseContent['phase'], $project->phase);
        assertEquals($responseContent['conclusion'], $project->conclusion);
        assertEquals($responseContent['uuid'], $project->uuid);
        assertEquals($responseContent['region']['name'], $project->region()->first()->name);
        assertEquals($responseContent['region']['uuid'], $project->region()->first()->uuid);
        assertEquals($responseContent['partner']['title'], $project->partner()->first()->title);
        assertEquals($responseContent['partner']['description'], $project->partner()->first()->description);
        assertEquals($responseContent['partner']['uuid'], $project->partner()->first()->uuid);
        assertEquals(Carbon::make($responseContent['startDate']), Carbon::make($project->start_date));
        assertEquals(Carbon::make($responseContent['publishedAt']), Carbon::make($project->published_at));
        assertEquals($responseContent['image']['url'], 'storage/' . $project->image()->first()->url);
    });


    it('faves a project', function () {
        $user = UpUsers::factory()->create(['uuid' => ApiTestHelper::$userUuid]);
        $project = Projects::factory()->create();

        $response = ApiTestHelper::makePatchRequest('/api/projects/' . $project->uuid . '/fav');
        $response->assertStatus(200);

        $responseContent = json_decode($response->content(), true)['data'];

        assertTrue($responseContent['isFaved']);
        assertEquals($project->uuid, $responseContent['uuid']);

        assertDatabaseHas('projects_users_favorited_links', [
            'project_id' => $project->id,
            'user_id' => $user->id,
        ]);

        assertCount(1, $user->projectsFavorited()->get());
        assertCount(1, $project->usersFavorited()->get());
        assertEquals($user->projectsFavorited()->first()->uuid, $project->uuid);
        assertEquals($project->usersFavorited()->first()->uuid, $user->uuid);
    });


    it('unfaves a project', function () {
        $user = UpUsers::factory()->create(['uuid' => ApiTestHelper::$userUuid]);
        $project = Projects::factory()->create();
        $project->usersFavorited()->attach($user);

        assertDatabaseHas('projects_users_favorited_links', [
            'project_id' => $project->id,
            'user_id' => $user->id,
        ]);

        $response = ApiTestHelper::makePatchRequest('/api/projects/' . $project->uuid . '/unfav');
        $response->assertStatus(200);

        $responseContent = json_decode($response->content(), true)['data'];

        assertFalse($responseContent['isFaved']);
        assertEquals($project->uuid, $responseContent['uuid']);

        assertDatabaseMissing('projects_users_favorited_links', [
            'project_id' => $project->id,
            'user_id' => $user->id,
        ]);
        assertCount(0, $project->usersFavorited()->get());
        assertCount(0, $user->projectsFavorited()->get());
    });


    it('joins a project', function () {
        $user = UpUsers::factory()->create(['uuid' => ApiTestHelper::$userUuid]);
        $project = Projects::factory()->create();

        $response = ApiTestHelper::makePatchRequest('/api/projects/' . $project->uuid . '/join');
        $response->assertStatus(200);

        $responseContent = json_decode($response->content(), true)['data'];

        assertCount(1, $responseContent);
        assertTrue($responseContent[0]['joined']);
        assertEquals($project->uuid, $responseContent[0]['uuid']);

        assertDatabaseHas('projects_users_joined_links', [
            'project_id' => $project->id,
            'user_id' => $user->id,
        ]);
        assertEquals($user->projectsJoined()->first()->uuid, $project->uuid);
        assertEquals($project->usersJoined()->first()->uuid, $user->uuid);
        assertCount(1, $user->projectsJoined()->get());
        assertCount(1, $project->usersJoined()->get());
    });


    it('leaves a project', function () {
        $user = UpUsers::factory()->create(['uuid' => ApiTestHelper::$userUuid]);
        $project = Projects::factory()->create();
        $project->usersJoined()->attach($user);

        assertDatabaseHas('projects_users_joined_links', [
            'project_id' => $project->id,
            'user_id' => $user->id,
        ]);
        assertEquals($user->projectsJoined()->first()->uuid, $project->uuid);
        assertEquals($project->usersJoined()->first()->uuid, $user->uuid);
        assertCount(1, $user->projectsJoined()->get());
        assertCount(1, $project->usersJoined()->get());

        $response = ApiTestHelper::makePatchRequest('/api/projects/' . $project->uuid . '/leave');
        $response->assertStatus(200);

        $responseContent = json_decode($response->content(), true)['data'];

        assertCount(1, $responseContent);
        assertFalse($responseContent[0]['joined']);
        assertEquals($project->uuid, $responseContent[0]['uuid']);

        assertDatabaseMissing('projects_users_joined_links', [
            'project_id' => $project->id,
            'user_id' => $user->id,
        ]);
        assertCount(0, $user->projectsJoined()->get());
        assertCount(0, $project->usersJoined()->get());
    });

});
