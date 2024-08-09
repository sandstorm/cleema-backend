<?php

namespace App\Http\Controllers;

use App\Http\Resources\ChallengesCollection;
use App\Http\Resources\ChallengesResource;
use App\Models\ChallengeGoalTypeSteps;
use App\Models\ChallengeImages;
use App\Models\Challenges;
use App\Models\Files;
use App\Models\JoinedChallenges;
use App\Models\JoinedChallengesAnswers;
use App\Models\Regions;
use App\Models\UpUsers;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use function PHPUnit\Framework\isEmpty;

class ChallengesController extends Controller
{
    /**
     * control method for /projects/fetch
     * @return ChallengesCollection
     */
    public function fetch()
    {
        $user = Auth::guard('localAuth')->user();
        // new filters
        $regionUUID = request('region');
        $type = request('type');
        $isJoined = request('joined');
        $kind = request('kind');
        $filters = request('filters');
        // We want the Challenges to not just vanish when the end_date is reached, so we subtract 1 week from the
        // current dateTime, so the Challenges are displayed for one more week (e.g. to see collective Challenge result)
        $dateTimeToLoadFor = Carbon::now()->subWeek();

        if (!$regionUUID && !$isJoined && $filters) {
            if (isset($filters['region']['uuid']['$eq'])) {
                $regionUUID = $filters['region']['uuid']['$eq'];
            }
        }

        if (!$kind && $filters) {
            if (isset($filters['kind'][array_key_first($filters['kind'])])) {
                $kind = explode(',', $filters['kind'][array_key_first($filters['kind'])]);
            }
        }

        if ($regionUUID && $kind) {
            $regions = Regions::where('is_supraregional', true)->pluck('uuid', 'id');
            $regions[] = $regionUUID;
            $challenges = Challenges::where('end_date', '>', $dateTimeToLoadFor)
                ->where('published_at', '<', Carbon::now())
                ->whereIn('kind', $kind)
                ->whereRelation('region', fn($query) => $query->whereIn('uuid', $regions))
                ->get();
        } else if ($isJoined) {
            $challenges = Challenges::where('end_date', '>', $dateTimeToLoadFor)
                ->whereHas('joinedChallenges', function ($query) use ($user) {
                    $query->where('user_id', '=', $user->id);
                })->get();
        } else {
            $challenges = Challenges::where('end_date', '>', $dateTimeToLoadFor)->where('published_at', '<', Carbon::now())->get()->filter();
        }
        return new ChallengesCollection($challenges);
    }

    /**
     * join a project when authenticated
     *
     */
    public function join(string $uuid)
    {
        $user = Auth::guard('localAuth')->user();
        if (!$user) {
            return Controller::getApiErrorMessage("Authentication failed");
        }

        $challenge = Challenges::where('uuid', '=', $uuid)->first();
        if (empty($challenge)) {
            return Controller::getApiErrorMessage("No challenge with uuid: " . $uuid . " found!");
        }

        if ($challenge->end_date->format('Y-m-d') < Carbon::now()->format('Y-m-d')) {
            return response()->json(Controller::getApiErrorMessage('Cannot join finished Challenge'), 409);
        }

        if ($user->joinedChallenges()->where('id', $challenge->id)->exists()) {
            return Controller::getApiErrorMessage('Challenge already joined.');
        }

        $challenge->joinedUsers()->attach($user, ['created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        $challenge->load('joinedUsers');
        $user->load('challengesJoined');

        return new ChallengesResource($challenge);
    }

    /**
     * leave a project when authenticated
     *
     */
    public function leave(string $uuid)
    {
        $user = Auth::guard('localAuth')->user();
        if (!$user) {
            return Controller::getApiErrorMessage("Authentication failed");
        }
        $challenge = Challenges::where('uuid', '=', $uuid)->first();
        if (empty($challenge)) {
            return Controller::getApiErrorMessage("no challenge with uuid: " . $uuid . " found!");
        }

        if ($challenge->end_date->format('Y-m-d') < Carbon::now()->format('Y-m-d')) {
            return response()->json(Controller::getApiErrorMessage('Cannot leave finished Challenge'), 409);
        }

        // remove joinedChallengeAnswers from user
        $answers = $challenge->joinedChallenges()->where('user_id', '=', $user->id)->first()->answers()->get();
        foreach ($answers as $answer) {
            $answer->delete();
        }

        $challenge->joinedUsers()->detach($user);
        $challenge->load('joinedUsers');
        $user->load('joinedChallenges');

        return new ChallengesResource($challenge);
    }

    public function fetchOne(string $uuid)
    {
        $challenge = Challenges::where('uuid', '=', $uuid)->first();
        if (!$challenge) {
            return response()->json(Controller::getApiErrorMessage("no challenge with uuid: " . $uuid . " found!"), 400);
        }
        return new ChallengesResource($challenge);
    }

    public function answer(string $uuid)
    {
        // check if challenge exists
        $challenge = Challenges::where('uuid', '=', $uuid)->first();
        if (!$challenge) {
            return response()->json(Controller::getApiErrorMessage("no challenge with uuid: " . $uuid . " found!"), 400);
        }

        // check if user is authenticated
        $user = Auth::guard('localAuth')->user();
        if (!$user) {
            return response()->json(Controller::getApiErrorMessage('Authentication failed.'), 400);
        }

        // check if joinedChallenge exists
        $joinedChallenge = $challenge->joinedChallenges()->where('user_id', '=', $user->id)->first();
        if (!$joinedChallenge) {
            return response()->json(Controller::getApiErrorMessage('Authenticated user has not joined the challenge.'), 400);
        }

        if ($challenge->end_date < Carbon::now()->format('Y-m-d')) {
            return response()->json(Controller::getApiErrorMessage('Cannot answer finished Challenge'), 409);
        }

        $answers = request()->post('answers');
        // create new JoinedChallengeAnswers for every answer which not already exists
        foreach ($answers as $answer) {
            if (!$joinedChallenge->answers()->where('day_index', '=', $answer['dayIndex'])->first()) {
                $joinedChallenge->answers()->create(['day_index' => $answer['dayIndex'], 'answer' => $answer['answer']]);
            }
        }
        return new ChallengesResource($challenge);
    }

    public function create()
    {
        // check if user is authenticated
        $user = Auth::guard('localAuth')->user();
        if (!$user) {
            return response()->json(Controller::getApiErrorMessage('Authentication failed.'), 400);
        }
        $data = request()->post('data');
        if (!$data) {
            return response()->json(Controller::getApiErrorMessage('Data missing'), 400);
        }

        $region = Regions::where('uuid', '=', $data['region']['uuid'])->first();
        $challenge = Challenges::create([
            'description' => $data['description'] ?: null,
            'end_date' => $data['endDate'] ?: null,
            'start_date' => $data['startDate'] ?: null,
            'kind' => $data['kind'] ?: null,
            'interval' => $data['interval'] ?: null,
            'is_public' => $data['isPublic'] ?? false,
            'teaser_text' => $data['teaserText'] ?: null,
            'title' => $data['title'] ?: null,
            'goal_type' => $data['goalType'] ?: null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'trophy_processed' => false,
            'uuid' => Str::uuid(),
            'views' => 0,
            'locale' => 'de-DE'
        ]);
        // set creator of the challenge to author
        $challenge->author()->associate($user);

        if(!empty($data['image'])){
            $image = Files::where('uuid', '=',$data['image']['uuid'])->first();
            if(!empty($image)){
                $challenge->image()->associate($image);
            }
        }

        // set region
        $challenge->region()->associate($region);
        if (!empty($data['participants'])) {
            foreach ($data['participants'] as $pUuid) {
                $participant = UpUsers::where('uuid', '=', $pUuid)->first();
                if (!$participant) {
                    continue;
                }
                $joinedChallenge = JoinedChallenges::create(['created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
                $joinedChallenge->challenge()->associate($challenge);
                $joinedChallenge->user()->associate($participant);
                $joinedChallenge->save();
            }
        }

        // create joined Challenge for user and challenge
        $joinedChallenge = JoinedChallenges::create(['created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        $joinedChallenge->user()->associate($user);
        $joinedChallenge->challenge()->associate($challenge);
        $joinedChallenge->save();
        $challenge->save();
        $challenge->load('joinedUsers');
        $user->save();

        // create goalSteps or / and goalMeasurement
        if (isset($data['goalSteps'])) {
            $challenge->goalTypeSteps()->create(['count' => $data['goalSteps']['count']]);
        }
        if (isset($data['goalMeasurement'])) {
            $challenge->goalTypeMeasurement()->create(['unit' => $data['goalMeasurement']['unit'], 'value' => $data['goalMeasurement']['value']]);
        }
        return ['data' => new ChallengesResource($challenge)];
    }
}
