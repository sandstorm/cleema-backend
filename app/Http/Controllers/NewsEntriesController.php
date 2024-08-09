<?php

namespace App\Http\Controllers;

use App\Http\Resources\NewsEntriesCollection;
use App\Http\Resources\NewsEntriesResource;
use App\Models\NewsEntries;
use App\Models\Regions;
use Illuminate\Support\Facades\Auth;

class NewsEntriesController extends Controller
{
    /**
     * fetch by region
     * @return NewsEntriesCollection
     */
    public function fetch ()
    {
        $user = Auth::guard('localAuth')->user();
        // TODO check filters again cuz this is not how its done by strapi
        // new filters
        $regionUUID = request('region');
        $type = request('type');
        $search = request('search');
        $isFaved = request('isFaved');

        if(!$regionUUID && !$type && !$search) {
            /* old strapi filters:
                Field("populate") { "tags" }
                Field("populate") { "image" }
                Optionally { Field("filters[$and][0][tags][value][$containsi]", default: nil) { wordsParser } }
                Optionally { Field("filters[$or][1][region][uuid][$eq]", default: nil) { UUID.parser() } }
                Field("filters[$or][2][type][$eq]", default: nil) { "tip" }
            */
            $filters = request('filters');
            if(isset($filters['$or']['1']['region']['uuid']['$eq'])) {
                $regionUUID = $filters['$or']['1']['region']['uuid']['$eq'];
            }
            if(isset($filters['$or']['2']['type']['$eq'])) {
                $type = $filters['$or']['2']['type']['$eq'];
            }
            if(isset($filters['$and'][0]['tags']['value']['$containsi'])){
                $search = $filters['$and'][0]['tags']['value']['$containsi'];
            }
        }

        if($regionUUID && $type && $search){
            // TODO Search mechanism
            $entries = NewsEntries::whereNotNull('published_at')->get();
        }
        else if($regionUUID && $type){
            $regions = Regions::where('is_supraregional', true)->pluck('uuid', 'id');
            $regions[] = $regionUUID;
            $entries = NewsEntries::whereNotNull('published_at')
                            ->where('published_at', '<', now())
                            ->whereNotNull('region_id')
                            ->whereRelation('region', fn($query) => $query->whereIn('uuid', $regions))
                            ->get();
        }
        else if($isFaved && $user){
            $entries = $user->favoritedNewsEntries()
                ->whereNotNull('published_at')
                ->get();
        }
        else if($regionUUID) {
            $entries = NewsEntries::whereNotNull('published_at')
                ->where('published_at', '<', now())
                ->where(function ($query) use ($regionUUID, $type) {
                    $query->whereRelation('region', 'uuid', '=', $regionUUID);
                })
                ->get();
        }
        else{
            $entries = NewsEntries::whereNotNull('published_at')->where('published_at', '<', now())->get();
        }
        return new NewsEntriesCollection($entries);
    }

    public function fetchOne(String $uuid) {
        $newsEntry = NewsEntries::where('uuid', '=', $uuid)->first();
        return new NewsEntriesResource($newsEntry);
    }

    /**
     * return a specific entry when user wants to read it
     * @param String $uuid
     * @return NewsEntriesResource|string[]
     */
    public function readEntry(String $uuid)
    {
        //get entry by uuid
        $entry = NewsEntries::where('uuid', '=', $uuid)->first();
        if(!$entry){
            return Controller::getApiErrorMessage('no newsEntry with uuid: ' . $uuid . ' found!');
        }
        // attach entry to read news entries of user
        $user = Auth::guard('localAuth')->user();
        if(!$user){
            return Controller::getApiErrorMessage("Authentication failed");
        }
        if(!$user->readNewsEntries()->get()->contains($entry)) {
            $user->readNewsEntries()->attach($entry->id);
        }

        if ($entry->views == null) {
            $entry->views = count($entry->usersRead()->get());
        }
        $entry->views++;
        $entry->update();
        // return read news entry for api
        return new NewsEntriesResource($entry);
    }

    /**
     * favourites an entry for authenticated users
     * @param String $uuid
     * @return NewsEntriesResource|string[]
     */
    public function favEntry (String $uuid)
    {
        $user = Auth::guard('localAuth')->user();
        if(!$user){
            return Controller::getApiErrorMessage("Authentication failed");
        }
        $newsEntry = NewsEntries::where('uuid', '=', $uuid)->first();
        if(empty($newsEntry)){
            return Controller::getApiErrorMessage('no newsEntry with uuid: ' . $uuid . ' found!');
        }
        if($user->favoritedNewsEntries->contains('id', $newsEntry->id)){
            return Controller::getApiErrorMessage('News Entry is already favourited.');
        }

        $newsEntry->usersFavorited()->attach($user);
        $newsEntry->load('usersFavorited');
        $user->load('favoritedNewsEntries');

        return new NewsEntriesResource($newsEntry);
    }

    /**
     * un-favourites an entry for authenticated users
     * @param String $uuid
     * @return NewsEntriesResource|string[]
     */
    public function unfavEntry (String $uuid)
    {
        $user = Auth::guard('localAuth')->user();
        if(!$user){
            return Controller::getApiErrorMessage("Authentication failed");
        }
        $newsEntry = NewsEntries::where('uuid', '=', $uuid)->first();
        if(empty($newsEntry)){
            return Controller::getApiErrorMessage('no newsEntry with uuid: ' . $uuid . ' found!');
        }
        if(!$user->favoritedNewsEntries->contains('id', $newsEntry->id)){
            return Controller::getApiErrorMessage('News Entry is not favourited.');
        }

        $newsEntry->usersFavorited()->detach($user);
        $newsEntry->load('usersFavorited');
        $user->load('favoritedNewsEntries');

        return new NewsEntriesResource($newsEntry);
    }
}
