<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProjectsCollection;
use App\Http\Resources\ProjectsResource;
use App\Models\NewsEntries;
use App\Models\Projects;
use App\Models\Regions;
use Illuminate\Support\Facades\Auth;

class ProjectsController extends Controller
{
    /**
     * control method for /projects/fetch
     * @return ProjectsCollection
     */
    public function fetch()
    {
        $user = Auth::guard('localAuth')->user();

        /*
         * Field("populate") { "*" }
         * Optionally { Field("filters[region][uuid][$eq]", default: nil) { UUID.parser() } }
         * Optionally { Field("isFaved", default: nil) { Bool.parser() } }
         */
        // new filters
        $regionUUID = request('region');
        $isFaved = null;

        // check user because we need an authenticated user to load favorited projects (anonymous or registered does not matter)
        if($user && request('isFaved') !== null){
            $isFaved = request('isFaved');
        }

        // old strapi filters:
        if(!$regionUUID) {
            $filters = request('filters');
            if (isset($filters['region']['uuid']['$eq'])) {
                $regionUUID = $filters['region']['uuid']['$eq'];
            }
        }

        // get projects with use of filters
        if(isset($regionUUID)){
            if($isFaved){
                $projects = Auth::guard('localAuth')->user()
                                ->projectsFavorited()
                                ->whereRelation('region', 'uuid', '=', $regionUUID)
                                ->where('phase', '!=', 'finished')->get();
            }
            else {
                $regions = Regions::where('is_supraregional', true)->pluck('uuid', 'id');
                $regions[] = $regionUUID;
                $projects = Projects::whereRelation('region',
                    fn($query) => $query->whereIn('uuid', $regions))
                    ->where('phase', '!=', 'finished')
                    ->get();
            }
        }
        else if($isFaved){
            $projects = Auth::guard('localAuth')->user()
                            ->projectsFavorited()
                            ->where('phase', '!=', 'finished')
                            ->get();
        }
        else{
            $projects = Projects::where('phase', '!=', 'finished')->get();
        }
        return new ProjectsCollection($projects);
    }

    public function fetchOne(String $uuid)
    {
        $proiect = Projects::where('uuid', '=', $uuid)->first();
        return new ProjectsResource($proiect);
    }

    /**
     * favourite a project when authenticated
     */
    public function fav(String $uuid)
    {
        $user = Auth::guard('localAuth')->user();
        if(!$user){
            return Controller::getApiErrorMessage("Authentication failed");
        }
        $project = Projects::where('uuid', '=', $uuid)->first();
        if(empty($project)){
            return Controller::getApiErrorMessage("no project with uuid: " . $uuid . " found!");
        }
        if($user->projectsFavorited->contains('id', $project->id)){
            return Controller::getApiErrorMessage('Project is already favourited');
        }

        $project->usersFavorited()->attach($user);
        $project->load('usersFavorited');
        $user->load('projectsFavorited');

        return new ProjectsResource($project);

    }

    /**
     * un-favourite a project when authenticated
     */
    public function unfav(String $uuid)
    {
        $user = Auth::guard('localAuth')->user();
        if(!$user){
            return Controller::getApiErrorMessage("Authentication failed");
        }

        $project = Projects::where('uuid', '=', $uuid)->first();
        if(empty($project)){
            return Controller::getApiErrorMessage("no project with uuid: " . $uuid . " found!");
        }

        if(!$user->projectsFavorited->contains('id', $project->id)){
            return Controller::getApiErrorMessage('Project is already unfavourited');
        }

        $project->usersFavorited()->detach($user);
        $project->load('usersFavorited');
        $user->load('projectsFavorited');

        return new ProjectsResource($project);
    }

    /**
     * join a project when authenticated
     * @return ProjectsCollection|string[]
     */
    public function join(String $uuid)
    {
        $user = Auth::guard('localAuth')->user();
        if(!$user){
            return Controller::getApiErrorMessage("Authentication failed");
        }
        $project = Projects::where('uuid', '=', $uuid)->first();
        if(empty($project)){
            return Controller::getApiErrorMessage("no project with uuid: " . $uuid . " found!");
        }

        if($user->projectsJoined->contains('id', $project->id)){
            return Controller::getApiErrorMessage('Already joined project');
        }

        $project->usersJoined()->attach($user);
        $project->load('usersJoined');
        $user->load('projectsJoined');

        // Check if a trophy must be awarded
        TrophiesController::checkProjectTrophies();

        return new ProjectsCollection([$project]);
    }

    /**
     * leave a project when authenticated
     * @return ProjectsCollection|string[]
     */
    public function leave(String $uuid)
    {
        $user = Auth::guard('localAuth')->user();
        if(!$user){
            return Controller::getApiErrorMessage("Authentication failed");
        }
        $project = Projects::where('uuid', '=', $uuid)->first();
        if(empty($project)){
            return Controller::getApiErrorMessage("no project with uuid: " . $uuid . " found!");
        }

        if(!$user->projectsJoined->contains('id', $project->id)){
            return Controller::getApiErrorMessage('Already left project');
        }

        $project->usersJoined()->detach($user);
        $project->load('usersJoined');
        $user->load('projectsJoined');

        return new ProjectsCollection([$project]);
    }
}
